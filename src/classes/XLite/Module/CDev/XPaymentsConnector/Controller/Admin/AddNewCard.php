<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */
 namespace XLite\Module\CDev\XPaymentsConnector\Controller\Admin;

/**
 * Add new credit card
 */
class AddNewCard extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return true;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Add new credit card');
    }

    /**
     * Get customer profile ID from request
     *
     * @return int
     */
    public function getCustomerProfileId()
    {
        return intval(\XLite\Core\Request::getInstance()->profile_id);
    }

    /**
     * Get customer profile
     *
     * @return \XLite\Model\Profile
     */
    protected function getCustomerProfile()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Profile')->find(
            $this->getCustomerProfileId()
        );
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getPaymentMethod()
            && $this->getCustomerProfile();
    }

    /**
     * Payment amount for zero-auth (card-setup)
     *
     * @return bool
     */
    public function getAmount()
    {
        return \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_zero_auth_amount;
    }

    /**
     * Payment description for zero-auth (card-setup)
     *
     * @return bool
     */
    public function getDescription()
    {
        return \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector->xpc_zero_auth_description;
    }

    /**
     * Get list of addresses
     *
     * @return array 
     */
    public function getAddressList()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getAddressList($this->getCustomerProfile());
    }

    /**
     * Get list of addresses
     *
     * @param \XLite\Model\Profile $profile Customer's profile
     *
     * @return bool
     */
    public function isSingleAddress()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->isSingleAddress($this->getCustomerProfile());
    }

    /**
     * Get string linne for the single address
     *
     * @param \XLite\Model\Profile $profile Customer's profile
     *
     * @return string
     */
    public function getSingleAddress(\XLite\Model\Profile $profile)
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getSingleAddress($this->getCustomerProfile());
    }

    /**
     * Get address ID
     *
     * return int
     */
    public function getAddressId()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getAddressId($this->getCustomerProfile());
    }

    /**
     * Show iframe redirect form
     *
     * @return void
     */
    protected function doActionXpcIframe()
    {
        $this->setReturnURL($this->buildURL('add_new_card'));

        \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->doActionXpcIframe(
            $this->getCustomerProfile(),
            \XLite::getAdminScript()
        );

    }

    /**
     * Update address (set selected address for the current zero auth)
     *
     * return void
     */
    protected function doActionUpdateAddress()
    {
        \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->doActionUpdateAddress($this->getCustomerProfile());
    }
}
