<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Sections;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Widget class of Shipping section of the fastlane checkout
 */
class Shipping extends \XLite\View\AView
{
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            array(
                $this->getDir() . '/component.js',
            )
        );
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            array(
                array(
                    'file'  => $this->getDir() . '/style.less',
                    'media' => 'screen',
                    'merge' => 'bootstrap/css/bootstrap.less',
                ),
            )
        );
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'sections/shipping';
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/template.twig';
    }

    protected function getNextButtonLabel()
    {
        return static::t('Proceed to payment');
    }

    protected function isEmailFieldVisible()
    {
        return !FastLaneCheckout\View\Sections::isAddressSectionNeeded();
    }

    protected function hasNonTemporaryAddress()
    {
        $profile = $this->getCart()->getProfile();

        if ($profile) {
            foreach ($profile->getAddresses() as $address) {
                if (false == $address->getIsWork()) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function isAddressBookVisible()
    {
        return $this->isLogged() && $this->hasNonTemporaryAddress();
    }
}
