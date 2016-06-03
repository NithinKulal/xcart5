<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

/**
 * Quantity widget
 */
class AddButton extends \XLite\View\Product\Details\Customer\Widget
{
    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-add-button';
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product/add_button/body.twig';
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
