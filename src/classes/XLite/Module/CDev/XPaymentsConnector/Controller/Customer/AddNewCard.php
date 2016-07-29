<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 namespace XLite\Module\CDev\XPaymentsConnector\Controller\Customer;

/**
 * Add new credit card
 *
 */
class AddNewCard extends \XLite\Controller\Customer\ACustomer
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
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return true;
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
            && (
                \XLite\Core\Auth::getInstance()->isLogged()
                || 'check_cart' == \XLite\Core\Request::getInstance()->action
                || 'callback' == \XLite\Core\Request::getInstance()->action
            );
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Add new credit card');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode('My account');
        $this->addLocationNode('Saved credit cards');
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
     * Get profile ID
     *
     * @return int
     */
    public function getProfileId()
    {
        return $this->getProfile()->getProfileId();
    }

    /**
     * Get list of addresses
     *
     * @return array 
     */
    public function getAddressList()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getAddressList($this->getProfile());
    }

    /**
     * Get address ID
     *
     * return int
     */
    public function getAddressId()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->getAddressId($this->getProfile());
    }

    /**
     * Update address (set selected address for the current zero auth)
     *
     * return void 
     */
    protected function doActionUpdateAddress()
    {
        \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->doActionUpdateAddress($this->getProfile());
    }

    /**
     * Show iframe redirect form
     *
     * @return void
     */
    protected function doActionXpcIframe()
    {
        if ($this->getAddressList()) {

            $this->setReturnURL($this->buildURL('add_new_card'));

            \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->doActionXpcIframe(
                $this->getProfile(),
                \XLite::getCustomerScript()
            );
        }

    }
    
    /**
     * Customer return action 
     *
     * @return void
     */
    protected function doActionReturn()
    {
        \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->doActionReturn();
    }

    /**
     * Callback from X-Payments 
     *
     * @return void
     */
    protected function doActionCallback()
    {
        \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->doActionCallback();
    }

    /**
     * Check cart callback 
     *
     * @return void
     */
    protected function doActionCheckCart()
    {
        \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->doActionCheckCart();
    }

}
