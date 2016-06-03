<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product;

/**
 * Quantity box
 */
class QuantityBox extends \XLite\View\Product\QuantityBox implements \XLite\Base\IDecorator
{
    /**
     * Return additional validate
     *
     * @return string
     */
    protected function getAdditionalValidate()
    {
        $maxValue = $this->getParam(self::PARAM_MAX_VALUE);
        $nonDefaultAmount = isset($maxValue)
            || (
                $this->getOrderItem()
                && $this->getOrderItem()->getVariant()
                && !$this->getOrderItem()->getVariant()->getDefaultAmount()
            );

        return $nonDefaultAmount || $this->getProduct()->getInventoryEnabled() ? ',max[' . $this->getMaxQuantity() . ']' : '';
    }

    /**
     * Return maximum allowed quantity
     *
     * @return integer
     */
    protected function getMaxQuantity()
    {
        return $this->getOrderItem() && $this->getOrderItem()->getVariant()
            ? $this->getOrderItem()->getVariant()->getAvailableAmount() + $this->getOrderItem()->getAmount()
            : parent::getMaxQuantity();
    }
}
