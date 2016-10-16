<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\Details\Customer;

/**
 * Stock
 */
class Stock extends \XLite\View\Product\Details\Customer\Stock implements \XLite\Base\IDecorator
{
    /**
     * Check if varant is out-of-stock
     *
     * @return boolean
     */
    protected function isShowStockWarning()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->isShowStockWarning()
            : parent::isShowStockWarning();
    }

    /**
     * Return available amount
     *
     * @return integer
     */
    protected function getAvailableAmount()
    {
        return $this->getProductVariant()
            ? $this->getProductVariant()->getAvailableAmount()
            : parent::getAvailableAmount();
    }

    /**
     * Check - 'items available' label is visible or not
     *
     * @return boolean
     */
    protected function isInStock()
    {
        $result = parent::isInStock();
        $variant = $this->getProductVariant();

        if ($variant) {
            if ($variant->getDefaultAmount()) {
                $result = $this->getProduct()->getInventoryEnabled() && !$variant->isOutOfStock();
            } else {
                $result = !$variant->isOutOfStock();
            }
        } elseif ($this->getProduct()->mustHaveVariants()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Return 'Out of stock' message
     *
     * @return string
     */
    protected function getOutOfStockMessage()
    {
        return $this->getProduct()->mustHaveVariants()
            ? static::t($this->getProductVariant() ? 'This item is out of stock' : 'This item is not available')
            : parent::getOutOfStockMessage();
    }
}
