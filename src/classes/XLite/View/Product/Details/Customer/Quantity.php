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
class Quantity extends \XLite\View\Product\Details\Customer\Widget
{
    const PARAM_QUANTITY = 'quantity';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_QUANTITY  => new \XLite\Model\WidgetParam\TypeInt('Value', null),
        );
    }

    /**
     * Alias
     *
     * @return integer
     */
    protected function getQuantity()
    {
        return $this->getParam(static::PARAM_QUANTITY);
    }

    /**
     * Define the CSS classes
     *
     * @return string
     */
    protected function getCSSClass()
    {
        return 'product-qty';
    }

    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product/quantity/body.twig';
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class
     *
     * @return null|string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-product-quantity';
    }

    /**
     * Return maximum allowed quantity
     *
     * @return integer
     */
    protected function getMaxQuantity()
    {
        return null;
    }
}
