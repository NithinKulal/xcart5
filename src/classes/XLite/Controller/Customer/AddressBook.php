<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Addresses management controller
 */
class AddressBook extends \XLite\Controller\Customer\ACustomer
{
    /**
     * address
     *
     * @var \XLite\Model\Address
     */
    protected $address = null;

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return true;
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Address book');
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return \XLite\Core\Request::getInstance()->widget && $this->checkAccess();
    }

    /**
     * getAddress
     *
     * @return \XLite\Model\Address
     */
    public function getAddress()
    {
        return $this->address = $this->getModelForm()->getModelObject();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        $result = parent::checkAccess()
            && \XLite\Core\Auth::getInstance()->isLogged()
            && $this->checkProfile();

        if ($result) {
            $address = $this->getAddress();
            $result = !$address || \XLite\Core\Auth::getInstance()->checkProfile($address->getProfile());
        }

        return $result;
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Profile
     */
    protected function getProfile()
    {
        return $this->getModelForm()->getModelObject()->getProfile() ?: new \XLite\Model\Profile();
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Address book');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('My account'));
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return '\XLite\View\Model\Address\Address';
    }

    /**
     * Save address
     *
     * @return boolean
     */
    protected function doActionSave()
    {
        $result = $this->getModelForm()->performAction('update');

        if ($result) {
            $this->setHardRedirect(true);
        }

        return $result;
    }

    /**
     * doActionDelete
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $address = $this->getAddress();

        if (isset($address)) {
            \XLite\Core\Database::getEM()->remove($address);
            \XLite\Core\Database::getEM()->flush();

            \XLite\Core\TopMessage::addInfo('Address has been deleted');
        }

        $this->setReturnURL(\Xlite\Core\Converter::buildURL('address_book'));
    }

    /**
     * doActionCancelDelete
     *
     * @return void
     */
    protected function doActionCancelDelete()
    {
        // Do nothing, action is needed just for redirection back
    }
}
