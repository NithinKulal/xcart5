<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Disable default one-page checkout in case of fastlane checkout
 */
class Sections extends \XLite\View\Tabs\AJsTabs
{
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            array(
                FastLaneCheckout\Main::getSkinDir() . 'sections/section_change_button.js',
                FastLaneCheckout\Main::getSkinDir() . 'sections/details/order_total.js',
                FastLaneCheckout\Main::getSkinDir() . 'sections/next_button.js',
                FastLaneCheckout\Main::getSkinDir() . 'sections/section.js',
                FastLaneCheckout\Main::getSkinDir() . 'sections/sections.js',
            )
        );
    }

    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            array(
                FastLaneCheckout\Main::getSkinDir() . 'sections/sliding-tabs.css',
                array(
                    'file'  => FastLaneCheckout\Main::getSkinDir() . 'sections/style.less',
                    'media' => 'screen',
                    'merge' => 'bootstrap/css/bootstrap.less',
                ),
            )
        );
    }

    /**
     * Checks whether the tabs navigation is visible, or not
     *
     * @return boolean
     */
    protected function isTabsNavigationVisible()
    {
        return false;
    }

    /**
     * Returns true if current profile has an addresses
     * @return boolean 
     */
    protected static function hasAnyCompleteAddress() 
    {
        $addresses = \XLite::getController()->getCart() && \XLite::getController()->getCart()->getProfile()
            ? \XLite::getController()->getCart()->getProfile()->getAddresses()
            : array();

        foreach ($addresses as $address) {
            if ($address->isCompleted(\XLite\Model\Address::SHIPPING)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks that each address of the cart is completed
     * 
     * @return boolean
     */
    protected static function hasUnfinishedAddress()
    {
        $cart = \XLite::getController()->getCart();
        $profile = $cart->getProfile();

        if ($profile) {
            $addresses = array(
                \XLite\Model\Address::SHIPPING => $profile->getShippingAddress(),
                \XLite\Model\Address::BILLING => $profile->getBillingAddress()
            );

            foreach ($addresses as $type => $address) {
                if (!$address || !$address->isCompleted($type)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns true if addresses section should be visible
     * @return boolean [description]
     */
    public static function isAddressSectionNeeded()
    {
        return \XLite::getController()->isAnonymous() || !static::hasAnyCompleteAddress() || static::hasUnfinishedAddress();
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $sections = array();

        if ($this->isAddressSectionNeeded()) {
            $sections['address'] = array(
                'weight'   => 100,
                'title'    => 'Addresses',
                'widget'   => 'XLite\Module\XC\FastLaneCheckout\View\Sections\Address',
                'index'    => 0,
                // 'paneClasses' => array('slide-left', 'in'),
            );
        }

        if ($this->isShippingNeeded()) {
            $sections['shipping'] = array(
                'weight'   => 200,
                'title'    => 'Shipping info',
                'widget'   => 'XLite\Module\XC\FastLaneCheckout\View\Sections\Shipping',
                'index'    => 1,
                // 'paneClasses' => array('slide-left'),
            );
        }

        $sections['payment'] = array(
            'weight'   => 300,
            'title'    => 'Payment',
            'widget'   => 'XLite\Module\XC\FastLaneCheckout\View\Sections\Payment',
            'index'    => 2,
            // 'paneClasses' => array('slide-left'),
        );

        return $sections;
    }
}
