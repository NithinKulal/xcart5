<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\Model;

/**
 * Order item
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Get price
     *
     * @return float
     */
    public function getClearPrice()
    {
        $this->setWholesaleValues();

        return parent::getClearPrice();
    }

    /**
     * Check if item has a wrong amount
     *
     * @return boolean
     */
    public function hasWrongAmount()
    {
        $minQuantity = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\MinQuantity')
            ->getMinQuantity(
                $this->getProduct(),
                $this->getOrder()->getProfile() ? $this->getOrder()->getProfile()->getMembership() : null
            );

        $minimumQuantity = $minQuantity ? $minQuantity->getQuantity() : 1;

        return parent::hasWrongAmount() || ($minimumQuantity > $this->getAmount());
    }

    /**
     * Check - item price is controlled by server or not
     *
     * @return boolean
     */
    public function isPriceControlledServer()
    {
        $result = parent::isPriceControlledServer();

        if (!$result && $this->getProduct()) {
            $model = \XLite\Core\Database::getRepo('XLite\Module\CDev\Wholesale\Model\WholesalePrice')
                ->findOneBy(array('product' => $this->getProduct()));
            $result = !!$model;
        }

        return $result;
    }

    /**
     * Set wholesale values
     *
     * @return void
     */
    public function setWholesaleValues()
    {
        $this->getProduct()->setWholesaleQuantity($this->getAmount());
        if ($this->getOrder() && $this->getOrder()->getProfile()) {
            $this->getProduct()->setWholesaleMembership(
                $this->getOrder()->getProfile()->getMembership() ?: false
            );
        }
    }
}
