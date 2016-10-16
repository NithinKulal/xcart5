<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Sections;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Widget class of Address section of the fastlane checkout
 */
class Address extends \XLite\View\AView
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
     * Get modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    protected function getShippingModifier()
    {
        if (null === $this->modifier) {
            $this->modifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        }

        return $this->modifier;
    }

    /**
     * Check - shipping system is enabled or not
     *
     * @return boolean
     */
    protected function isShippingEnabled()
    {
        return $this->getShippingModifier() && $this->getShippingModifier()->canApply();
    }

    /**
     * @return string
     */
    protected function getBillingFormTitle()
    {
        return static::t('Billing address');
    }

    /**
     * @return string
     */
    protected function getShippingFormTitle()
    {
        return static::t('Shipping address');
    }

    protected function getNextButtonLabel()
    {
        return $this->isShippingEnabled()
             ? static::t('Choose shipping')
             : static::t('Proceed to payment');
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'sections/address';
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/template.twig';
    }
}
