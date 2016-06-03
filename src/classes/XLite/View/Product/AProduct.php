<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

/**
 * Abstract product-based widget
 */
abstract class AProduct extends \XLite\View\AView
{
    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'product';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Check if the product is in-stock
     *
     * @return boolean
     */
    protected function isInStock()
    {
        return $this->getProduct()->getInventoryEnabled()
            && !$this->getProduct()->isOutOfStock();
    }

    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    protected function isOutOfStock()
    {
        return $this->getProduct()->isOutOfStock();
    }

    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    protected function isShowStockWarning()
    {
        return $this->getProduct()->isShowStockWarning();
    }

    /**
     * Return product amount available to add to cart
     *
     * @return integer
     */
    protected function getAvailableAmount()
    {
        return $this->getProduct()->getAvailableAmount();
    }

    /**
     * Check - product is available for sale or not
     * 
     * @return boolean
     */
    protected function isProductAvailableForSale()
    {
        return $this->getProduct()->isAvailable();
    }

    /**
     * Checks whether a product was added to the cart
     *
     * @return boolean
     */
    protected function isProductAdded()
    {
        return $this->getCart()->isProductAdded($this->getProduct()->getProductId());
    }
}
