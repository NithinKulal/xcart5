<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Shipping;

/**
 * Add shipping method popup button
 */
class AddMethod extends \XLite\View\Button\APopupButton
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'shipping/add_method/style.css';

        $onlineCarrierLink = new \XLite\View\Button\Shipping\OnlineCarrier();
        $list = array_merge($list, $onlineCarrierLink->getCSSFiles());

        $shippingTypes = new \XLite\View\Tabs\ShippingType();
        $list = array_merge($list, $shippingTypes->getCSSFiles());

        $shippingMarkups = new \XLite\View\ItemsList\Model\Shipping\Markups();
        $list = array_merge($list, $shippingMarkups->getCSSFiles());

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/shipping/add_method.js';

        $list[] = 'shipping/add_method/controller.js';

        $onlineCarrierLink = new \XLite\View\Button\Shipping\OnlineCarrier();
        $list = array_merge($list, $onlineCarrierLink->getJSFiles());

        $shippingTypes = new \XLite\View\Tabs\ShippingType();
        $list = array_merge($list, $shippingTypes->getJSFiles());

        $shippingMarkups = new \XLite\View\ItemsList\Model\Shipping\Markups();
        $list = array_merge($list, $shippingMarkups->getJSFiles());

        return $list;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target' => 'shipping_method_selection',
            'widget' => 'XLite\View\Shipping\AddMethod',
        );
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Add shipping method';
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' add-shipping-method-button';
    }
}
