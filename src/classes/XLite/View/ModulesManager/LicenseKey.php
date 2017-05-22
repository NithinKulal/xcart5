<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager;

/**
 * Activate license key page
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class LicenseKey extends \XLite\View\ModulesManager\AModulesManager
{
    const KEY_NOTICE_VERSION_1 = 1;
    const KEY_NOTICE_VERSION_2 = 2;

    /**
     * @return int
     */
    public static function getLicenseKeyVersion()
    {
        return static::KEY_NOTICE_VERSION_2;
    }

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'activate_key';

        return $result;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getDir() . '/css/style.css';

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
        if (static::getLicenseKeyVersion() === static::KEY_NOTICE_VERSION_1) {
        } else {
            $list[] = $this->getDir() . '/controller.js';
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
        return static::getLicenseKeyVersion() === static::KEY_NOTICE_VERSION_1
            ? parent::getDir() . LC_DS . 'activate_key'
            : parent::getDir() . LC_DS . 'activate_key_v2';
    }

    /**
     * URL of the page where license can be purchased
     *
     * @return string
     */
    protected function getPurchaseURL()
    {
        return \XLite\Core\Marketplace::getPurchaseURL();
    }

    /**
     * URL of the X-Cart company's Contact Us page
     *
     * @return string
     */
    protected function getContactUsURL()
    {
        return \XLite\Core\Marketplace::getContactUsURL();
    }

    /**
     * Check if module activation
     *
     * @return boolean
     */
    protected function isModuleActivation()
    {
        return (bool) \XLite\Core\Request::getInstance()->isModule;
    }
}
