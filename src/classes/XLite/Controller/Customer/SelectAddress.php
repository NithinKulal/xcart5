<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Select address from address book
 */
class SelectAddress extends \XLite\Controller\Customer\Cart
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'atype');


    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Address book');
    }

    /**
     * Get current aAddress id
     *
     * @return integer|void
     */
    public function getCurrentAddressId()
    {
        $address = null;

        if ($this->getCart()->getProfile()) {
            $address = \XLite\Model\Address::SHIPPING == \XLite\Core\Request::getInstance()->atype
                ? $this->getCart()->getProfile()->getShippingAddress()
                : $this->getCart()->getProfile()->getBillingAddress();
        }

        return $address ? $address->getAddressId() : null;
    }


    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return parent::checkAccess()
            && \XLite\Core\Auth::getInstance()->isLogged();
    }

    /**
     * Select address
     *
     * @return void
     */
    protected function doActionSelect()
    {
        $atype = \XLite\Core\Request::getInstance()->atype;
        $addressId = \XLite\Core\Request::getInstance()->addressId;
        $hasEmptyFields = \XLite\Core\Request::getInstance()->hasEmptyFields === 'true'
            ? true
            : false;

        $this->selectCartAddress($atype, $addressId, $hasEmptyFields);
    }

    /**
     * Controller marks the cart calculation.
     * On the checkout page we need cart recalculation
     *
     * @return boolean
     */
    protected function markCartCalculate()
    {
        return true;
    }

    /**
     * Get cart fingerprint exclude keys
     *
     * @return array
     */
    protected function getCartFingerprintExclude()
    {
        return array();
    }
}
