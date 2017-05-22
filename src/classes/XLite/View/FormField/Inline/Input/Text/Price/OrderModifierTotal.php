<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Price;

/**
 * Order surcharge widget for AOM
 */
class OrderModifierTotal extends \XLite\View\FormField\Inline\Input\Text\Price\AbsPrice
{
    /**
     * Set widget params
     *
     * @param array $params Handler params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if ($this->getParam(static::PARAM_ENTITY) && !$this->getParam(static::PARAM_FIELD_NAME)) {
            $this->getWidgetParams(static::PARAM_FIELD_NAME)->setValue(
                $this->getParam(static::PARAM_ENTITY)->getCode()
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected function getCurrency()
    {
        return $this->getEntity() && $this->getEntity()->getOrder()
            ? $this->getEntity()->getOrder()->getCurrency()
            : parent::getCurrency();
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        return parent::getFieldParams($field) + array(
            \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MIN              => 0,
            \XLite\View\FormField\Input\Text\Base\Numeric::PARAM_MOUSE_WHEEL_CTRL => false,
        );
    }

    /**
     * Get field name parts
     *
     * @param array $field Field
     *
     * @return array
     */
    protected function getNameParts(array $field)
    {
        return array(
            'orderModifiers',
            $this->getParam(static::PARAM_FIELD_NAME),
        );
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        $value = $this->getEntity()->getValue();
        if ($value < 0 && $this->getEntity()->getType() === \XLite\Model\Base\Surcharge::TYPE_DISCOUNT) {
            $value = abs($value);
        }

        return $value;
    }

    /**
     * Save field value to entity
     *
     * @param array $field Field
     * @param mixed $value Value
     *
     * @return void
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        $data = \XLite\Core\Request::getInstance()->getPostData();

        $currency = $this->getEntity()->getOrder()->getCurrency();
        $isPersistent = $this->getEntity()->isPersistent();

        if ($this->getEntity()->getModifier()
            && !empty($data['auto']['surcharges'][$this->getEntity()->getCode()]['value'])
        ) {
            if (\XLite\Logic\Order\Modifier\Shipping::MODIFIER_CODE === $this->getEntity()->getCode()) {
                // Reset selected rate to avoid cache
                $this->getEntity()->getModifier()->resetSelectedRate();
                $this->getEntity()->getModifier()->setMode(\XLite\Logic\Order\Modifier\AModifier::MODE_CART);
            }

            // Calculate surcharge and get new surcharge object or array of surcharge objects
            $surcharges = $this->getEntity()->getModifier()->canApply()
                ? $this->getEntity()->getModifier()->calculate()
                : array();

            if (!is_array($surcharges)) {
                $surcharges = $surcharges ? array($surcharges) : array();
            }

            $value = 0;

            foreach ($surcharges as $surcharge) {
                if (is_object($surcharge)) {

                    if ($surcharge->getCode() === $this->getEntity()->getCode()) {
                        $value += $surcharge->getValue();
                    }

                    // Remove added surcharges if current entity exists in DB to avoid duplicates
                    if ($isPersistent && !$this->getEntity()->getModifier()->isIgnoreDuplicates()) {
                        \XLite\Core\Database::getEM()->remove($surcharge);
                        $surcharge->getOrder()->removeSurcharge($surcharge);
                    }
                }
            }

        } elseif (0 != $value && !$isPersistent) {

            $addSurcharge = true;

            // Search for current surcharge in order surcharges
            foreach ($this->getEntity()->getOrder()->getSurcharges() as $s) {
                if ($s->getCode() == $this->getEntity()->getCode()) {
                    $addSurcharge = false;
                }
            }

            if ($addSurcharge) {
                // Surcharge is new for order - add this
                $this->addOrderSurcharge($this->getEntity(), $value);
            }
        }

        if (0 < $value && $this->getEntity()->getType() === \XLite\Model\Base\Surcharge::TYPE_DISCOUNT) {
            $value = $value * -1;
        }

        $oldValue = $currency->roundValue($this->getEntity()->getValue());
        $newValue = $currency->roundValue($value);

        if ($oldValue !== $newValue) {
            \XLite\Controller\Admin\Order::setOrderChanges(
                $this->getParam(static::PARAM_FIELD_NAME),
                static::formatPrice(abs($value), $currency, true),
                static::formatPrice(abs($this->getEntity()->getValue()), $currency, true)
            );
        }

        if ($this->getEntity()->getType() === \XLite\Model\Base\Surcharge::TYPE_DISCOUNT) {
            $this->getEntity()->getModifier()->distributeDiscount($value * -1);
        }

        $this->getEntity()->setValue($value);
    }

    /**
     * Add order surcharge
     *
     * @param \XLite\Model\Order\Modifier $modifier Order modifier
     * @param float                       $value    Surcharge value
     *
     * @return void
     */
    protected function addOrderSurcharge($modifier, $value)
    {
        if (0 < $value && $modifier->getType() === \XLite\Model\Base\Surcharge::TYPE_DISCOUNT) {
            $value = $value * -1;
        }

        $modifier->getModifier()->addOrderSurcharge(
            $modifier->getCode(),
            (float) $value
        );
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function isEditable()
    {
        return !$this->getViewOnly() && ($this->getEditOnly() || $this->getEntity());
    }
}
