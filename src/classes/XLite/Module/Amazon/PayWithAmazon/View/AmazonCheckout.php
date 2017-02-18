<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View;

use XLite\Core\Request;
use XLite\Model\Base\Surcharge;

/**
 * Amazon checkout widget
 *
 * @ListChild (list="center", zone="customer")
 */
class AmazonCheckout extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['amazon_checkout']);
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Amazon/PayWithAmazon/checkout.css';

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'checkout/js/controller.js';

        return $list;
    }

    /**
     * Required by XC\MailChimp
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return \XLite::getController()->getCart()->getProfile();
    }

    /**
     * Check if order has only non-shippable products
     *
     * @return boolean
     */
    public function isOrderShippable()
    {
        $cart = \XLite::getController()->getCart();
        $modifier = $cart->getModifier(Surcharge::TYPE_SHIPPING, 'SHIPPING');

        return $modifier && $modifier->canApply();
    }

    /**
     * @return string
     */
    public function getOrderReference()
    {
        return Request::getInstance()->orderReference ?: '';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Amazon/PayWithAmazon/checkout.twig';
    }
}
