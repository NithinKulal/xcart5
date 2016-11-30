<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\Controller\Customer;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Disable default one-page checkout in case of fastlane checkout
 */
class Checkout extends \XLite\Controller\Customer\Checkout implements \XLite\Base\IDecorator
{
    /**
     * Shipping address before profile update
     *
     * @var \XLite\Model\Address
     */
    protected $originalShippingAddress;

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return !FastLaneCheckout\Main::isFastlaneEnabled();
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->widget_title ?: parent::getTitle();
    }

    /**
     * Get 'Terms and conditions' page URL
     *
     * @return string
     */
    public function getTermsURL()
    {
        return \XLite\Core\Config::getInstance()->General->terms_url;
    }


    /**
     * Prepares shipping address to update
     * 
     * @return \XLite\Model\Address
     */
    protected function prepareShippingAddress()
    {
        $address = parent::prepareShippingAddress();

        if ($address) {
            $this->originalShippingAddress = $address->cloneEntity();
        }

        return $address;
    }

    /**
     * Separate shipping and billing addresses
     */
    protected function unlinkShippingFromBilling()
    {
        parent::unlinkShippingFromBilling();

        if ($this->originalShippingAddress) {
            $profile = $this->getCart()->getProfile();

            $original = $this->originalShippingAddress;

            $original->setIsShipping(false);
            $original->setIsBilling(true);
            $original->setIsWork(true);
            $original->setProfile($profile);
            $profile->addAddresses($original);

            \XLite\Core\Database::getEM()->persist($original);

            $this->originalShippingAddress = null;
        }
    }
}
