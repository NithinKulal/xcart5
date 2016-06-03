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
            ? array(
                'trial_notice', // the popup window target
                'order',
                'order_list',
            ) : array(
                'trial_notice',
            );
    }

    /**
     * In the customer area the following targets are allowed.
     *
     * @return array
     */
    public static function getAllowedCustomerTargetsTrialNotice()
    {
        return array(
            'trial_notice',
            'main',
            'checkout',
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/css/style.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'trial_notice';
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
     * URL of the X-Cart company's License Agreement page
     *
     * @return string
     */
    protected function getLicenseAgreementURL()
    {
        return \XLite\Core\Marketplace::getLicenseAgreementURL();
    }
    
}
