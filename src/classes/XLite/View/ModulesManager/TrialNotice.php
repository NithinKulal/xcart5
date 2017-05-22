<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager;

/**
 * Trial notice page
 *
 * @ListChild (list="admin.center", zone="admin", weight=0)
 */
class TrialNotice extends \XLite\View\ModulesManager\AModulesManager
{
    const TRIAL_NOTICE_VERSION_1 = 1;
    const TRIAL_NOTICE_VERSION_2 = 2;

    /**
     * @return int
     */
    public static function getTrialNoticeVersion()
    {
        return static::TRIAL_NOTICE_VERSION_2;
    }

    /**
     * The allowed targets for the trial notice is defined
     * in the static::getAllowedTargetsTrialNotice() method
     *
     * @see static::getAllowedTargetsTrialNotice()
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            static::getAllowedTargetsTrialNotice()
        );
    }

    /**
     * The allowed targets for admin area are defined in the static::getAllowedAdminTargetsTrialNotice()
     * The allowed targets for customer area are defined in the static::getAllowedCustomerTargetsTrialNotice()
     *
     * @see static::getAllowedAdminTargetsTrialNotice()
     * @see static::getAllowedCustomerTargetsTrialNotice()
     *
     * @return array
     */
    public static function getAllowedTargetsTrialNotice()
    {
        return \XLite::isAdminZone()
            ? static::getAllowedAdminTargetsTrialNotice()
            : static::getAllowedCustomerTargetsTrialNotice();
    }

    /**
     * In the admin area the following targets are allowed.
     *
     * @return array
     */
    public static function getAllowedAdminTargetsTrialNotice()
    {
        return \XLite::isTrialPeriodExpired()
            ? [
                'trial_notice', // the popup window target
                'order',
                'order_list',
                'product_list',
            ] : [
                'trial_notice',
            ];
    }

    /**
     * In the customer area the following targets are allowed.
     *
     * @return array
     */
    public static function getAllowedCustomerTargetsTrialNotice()
    {
        return [
            'trial_notice',
            'main',
            'checkout',
        ];
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
        if (static::getTrialNoticeVersion() === static::TRIAL_NOTICE_VERSION_1) {
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
        return static::getTrialNoticeVersion() === static::TRIAL_NOTICE_VERSION_1
            ? 'trial_notice'
            : 'trial_notice_v2';
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
     * @return string
     */
    protected function getRegisterLicenseURL()
    {
        return \XLite\Core\Converter::buildURL('', '', ['activate_key' => true], \XLite::ADMIN_SELF);
    }

    /**
     * URL of the X-Cart company's License Agreement page
     *
     * @return string
     */
    protected function getLicenseAgreementURL()
    {
        return \XLite\Core\Marketplace::getLicenseAgreementURL();
    }

    /**
     * @return boolean
     */
    protected function isPopup()
    {
        return \XLite::getController()->getTarget() === 'trial_notice';
    }

    /**
     * @return boolean
     */
    protected function isTrialPeriodExpired()
    {
        return \XLite::isTrialPeriodExpired();
    }
}
