<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

/**
 * Stock
 */
class Stock extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-stock';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product/details/stock/body.twig';
    }

    /**
     * Return available amount
     *
     * @return integer
     */
    protected function getAvailableAmount()
    {
        return $this->getProduct()->getAvailableAmount();
    }

    /**
     * Return 'Out of stock' message
     *
     * @return string
     */
    protected function getOutOfStockMessage()
    {
        return static::t('Out of stock');
    }
}
