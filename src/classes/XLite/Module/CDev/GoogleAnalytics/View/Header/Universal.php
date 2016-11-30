<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View\Header;

use XLite\Module\CDev\GoogleAnalytics;

/**
 * Header declaration (Universal)
 *
 * @ListChild (list="head", zone="customer")
 * @ListChild (list="head", zone="admin")
 */
class Universal extends \XLite\Module\CDev\GoogleAnalytics\View\Header\AHeader
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoogleAnalytics/header/universal.twig';
    }

    /**
     * @return bool
     */
    public function isDebugMode()
    {
        return GoogleAnalytics\Main::isDebugMode();
    }

    /**
     * @return string
     */
    public function getGAScriptURL()
    {
        return $this->isDebugMode()
            ? '//www.google-analytics.com/analytics_debug.js'
            : '//www.google-analytics.com/analytics.js';
    }

    /**
     * Get GA settings
     *
     * @return array
     */
    protected function getGASettings()
    {
        $group = \XLite\Core\Config::getInstance()->CDev->GoogleAnalytics;
        $currencyCode = \XLite::getInstance()->getCurrency()
            ? \XLite::getInstance()->getCurrency()->getCode()
            : 'USD';

        return [
            'isDebug'       => $this->isDebugMode(),
            'addTrace'      => $this->isDebugMode(),
            'account'       => $group->ga_account,
            'trackingType'  => $group->ga_tracking_type,
            'sendPageview'  => $this->isSendPageviewActive(),
            'currency'      => $currencyCode,
        ];
    }

    protected function isSendPageviewActive()
    {
        return !\XLite::isAdminZone();
    }

    /**
     * Get GA options list
     *
     * @return array
     */
    protected function getGAOptions()
    {
        return [];
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && GoogleAnalytics\Main::useUniversalAnalytics()
            && (
                ($this->isVisibleForCustomer() && !\XLite::isAdminZone())
                || ($this->isVisibleForAdmin() && \XLite::isAdminZone())
            );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisibleForCustomer()
    {
        return true;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisibleForAdmin()
    {
        return GoogleAnalytics\Main::isECommerceEnabled();
    }
}
