<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Checkout
 *
 * @ListChild (list="center")
 */
class Checkout extends \XLite\View\Dialog
{
    /**
     * Indexes in step data array
     */
    const STEP_TEMPLATE  = 'template';
    const STEP_SHOW_CART = 'showCart';


    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'checkout';

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'checkout/css/checkout.css';
        if (
            $checkoutCanceled = \XLite\Core\Session::getInstance()->checkoutCanceled
            && \XLite\Core\Request::getInstance()->checkoutCanceled
        ) {
            $list[] = 'back_from_payment/style.css';
        }

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     * FIXME - decompose these files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'checkout/js/controller.js';
        $list[] = 'button/js/login.js';
        if (
            $checkoutCanceled = \XLite\Core\Session::getInstance()->checkoutCanceled
            && \XLite\Core\Request::getInstance()->checkoutCanceled
        ) {
            $list[] = 'back_from_payment/controller.js';
        }

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'js/core.popup.js';
        $list[static::RESOURCE_JS][] = 'js/core.popup_button.js';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'checkout';
    }

    /**
     * Get preloaded labels
     *
     * @return array
     */
    protected function getPreloadedLabels()
    {
        $list = array(
            'Enter a correct email',
            'Order can not be placed because not all required fields are completed. Please check the form and try again.',
            'Field is required!',
        );

        $data = array();
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }

    /**
     * Defines the store name
     *
     * @return string
     */
    protected function getStoreName()
    {
        return \XLite\Core\Config::getInstance()->Company->company_name;
    }

    /**
     * Defines the anonymous title box
     *
     * @return string
     */
    protected function getSigninAnonymousTitle()
    {
        return static::t('Go to checkout as a New customer');
    }
}
