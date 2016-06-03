<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View;

/**
 * Paypal settings dialog
 */
class Settings extends \XLite\View\Dialog
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'paypal_settings';
        $list[] = 'paypal_credit';

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Paypal/settings/style.css';

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

        $api = \XLite\Module\CDev\Paypal\Main::getRESTAPIInstance();
        if ($api->isInContextSignUpAvailable()) {
            $list[] = 'modules/CDev/Paypal/settings/signup.js';
        }

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/CDev/Paypal/settings/' . $this->getMethodServiceName();
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getPaymentMethod();
    }

    /**
     * Return payment method service name
     *
     * @return string
     */
    protected function getMethodServiceName()
    {
        return $this->getPaymentMethod()->getServiceName();
    }
}
