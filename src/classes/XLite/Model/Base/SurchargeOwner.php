<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

/**
 * Surcharge owner
 *
 * @MappedSuperclass
 */
abstract class SurchargeOwner extends \XLite\Model\AEntity
{
    /**
     * Total
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $total = 0.0000;

    /**
     * Subtotal
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $subtotal = 0.0000;

    /**
     * Set total
     *
     * @param float $total Total
     *
     * @return \XLite\Model\Base\SurchargeOwner
     */
    public function setTotal($total)
    {
        $this->total = round($total, 4);

        return $this;
    }

    /**
     * Set subtotal
     *
     * @param float $subtotal Subtotal
     *
     * @return \XLite\Model\Base\SurchargeOwner
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = round($subtotal, 4);

        return $this;
    }

    // {{{ Saved surcharges

    /**
     * Get exclude surcharges (non-included)
     *
     * @return array
     */
    public function getExcludeSurcharges()
    {
        $list = array();

        foreach ($this->getSurcharges() as $surcharge) {
            if (!$surcharge->getInclude()) {
                $list[] = $surcharge;
            }
        }

        return $list;
    }

    /**
     * Get included surcharges
     *
     * @return array
     */
    public function getIncludeSurcharges()
    {
        $list = array();

        foreach ($this->getSurcharges() as $surcharge) {
            if ($surcharge->getInclude()) {
                $list[] = $surcharge;
            }
        }

        return $list;
    }

    /**
     * Get exclude surcharges (non-included) by type
     *
     * @param string $type Type
     *
     * @return array
     */
    public function getExcludeSurchargesByType($type)
    {
        $list = array();

        foreach ($this->getSurcharges() as $surcharge) {
            if (!$surcharge->getInclude() && $surcharge->getType() === $type) {
                $list[] = $surcharge;
            }
        }

        return $list;
    }



    /**
     * Get surcharge totals
     *
     * @return array
     */
    public function getSurchargeTotals()
    {
        return $this->calculateSurchargeTotals($this->getExcludeSurcharges());
    }

    /**
     * Get surcharge totals
     *
     * @return array
     */
    public function getCompleteSurchargeTotals()
    {
        return $this->calculateSurchargeTotals(
            array_merge($this->getExcludeSurcharges(), $this->getIncludeSurcharges())
        );
    }

    /**
     * Get surcharge totals
     *
     * @param array $surcharges
     *
     * @return array
     */
    public function calculateSurchargeTotals($surcharges)
    {
        $list = array();

        foreach ($surcharges as $surcharge) {
            $code = $surcharge->getCode();

            if (!isset($list[$code])) {
                $modifierClass = $surcharge->getClass();

                $list[$code] = array(
                    'name'      => $surcharge->getTypeName(),
                    'cost'      => 0,
                    'available' => $surcharge->getAvailable(),
                    'count'     => 0,
                    'lastName'  => null,
                    'code'      => $code,
                    'widget'    => \Includes\Utils\Operator::checkIfClassExists($modifierClass)
                        ? $modifierClass::getWidgetClass()
                        : \XLite\Logic\Order\Modifier\AModifier::getWidgetClass(),
                    'object'    => $surcharge,
                );
            }

            $list[$code]['cost'] += $surcharge->getValue();
            $list[$code]['count']++;
            $list[$code]['lastName'] = $surcharge->getName();
            $list[$code]['weight'] = $surcharge->getSortingWeight();
        }

        uasort($list, function ($a, $b) {
            return $a['weight'] < $b['weight'] ? -1 : $a['weight'] > $b['weight'];
        });

        return $list;
    }

    /**
     * Get surcharge sum
     *
     * @return float
     */
    public function getSurchargeSum()
    {
        $total = 0;

        foreach ($this->getExcludeSurcharges() as $s) {
            $total += $s->getValue();
        }

        return $total;
    }

    /**
     * Get surcharge sum by type
     *
     * @param string $type Surcharge type
     *
     * @return float
     */
    public function getSurchargeSumByType($type)
    {
        $total = 0;

        foreach ($this->getExcludeSurchargesByType($type) as $s) {
            $total += $s->getValue();
        }

        return $total;
    }

    /**
     * Get surcharge total by type
     *
     * @param string $type Surcharge type
     *
     * @return float
     */
    public function getSurchargeTotalByType($type)
    {
        return $this->getSubtotal() + $this->getSurchargeSumByType($type);
    }

    // }}}

    // {{{ Surcharge compare

    /**
     * Reset surcharges list
     *
     * @return \XLite\Model\Order\Surcharge[]
     */
    public function resetSurcharges()
    {
        $result = $this->getSurcharges()->toArray();
        foreach ($result as $k => $surcharge) {
            if ($this->resetSurcharge($surcharge)) {
                $this->removeSurcharge($surcharge);
            } else {
                unset($result[$k]);
            }
        }

        return $result;
    }

    /**
     * Reset surcharge
     *
     * @param \XLite\Model\Order\Surcharge $surcharge Surcharge
     *
     * @return \XLite\Model\Order\Surcharge
     */
    public function resetSurcharge($surcharge)
    {
        $surcharge->setOwner(null);

        return $surcharge;
    }

    /**
     * Remove surcharge
     *
     * @param \XLite\Model\Order\Surcharge $surcharge Surcharge
     *
     * @return void
     */
    public function removeSurcharge($surcharge)
    {
        $this->surcharges->removeElement($surcharge);
    }

    /**
     * Compare and merge surcharges
     *
     * @param array $oldSurcharges Old surcharges
     *
     * @return array
     */
    public function compareSurcharges(array $oldSurcharges)
    {
        foreach ($oldSurcharges as $i => $oldSurcharge) {
            foreach ($oldSurcharges as $i2 => $oldSurcharge2) {
                if ($i !== $i2 && $oldSurcharge->isEqualSurcharge($oldSurcharge2)) {
                    unset($oldSurcharges[$i2]);
                    \XLite\Core\Database::getEM()->remove($oldSurcharge2);

                }
            }
        }

        foreach ($oldSurcharges as $i => $oldSurcharge) {
            foreach ($this->getSurcharges() as $surcharge) {
                if ($surcharge->isEqualSurcharge($oldSurcharge)) {
                    $oldSurcharge->replaceSurcharge($surcharge);
                    unset($oldSurcharges[$i]);
                    break;
                }
            }
        }

        foreach ($oldSurcharges as $surcharge) {
            \XLite\Core\Database::getEM()->remove($surcharge);
        }

        return $oldSurcharges;
    }

    // }}}
}
