<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Checkout\Step;

/**
 * Shipping step
 */
class Shipping extends \XLite\View\Checkout\Step\AStep
{
    /**
     * Modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $modifier;

    /**
     * Get step name
     *
     * @return string
     */
    public function getStepName()
    {
        return 'shipping';
    }

    /**
     * Get step title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Shipping / Payment info';
    }

    /**
     * Check - step is complete or not
     *
     * @return boolean
     */
    public function isCompleted()
    {
        return $this->isShippingCompleted() && $this->isPaymentCompleted();
    }

    /**
     * Check - step is enabled (true) or skipped (false)
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return parent::isEnabled() && ($this->isShippingEnabled() || $this->isPaymentEnabled());
    }

    /**
     * Check - shipping substep is complete or not
     *
     * @return boolean
     */
    protected function isProfileCompleted()
    {
        return $this->getCart()->getProfile()->getLogin()
            && (
                !$this->getCart()->getProfile()->getAnonymous()
                || !\XLite\Core\Session::getInstance()->order_create_profile
                || \XLite\Core\Session::getInstance()->createProfilePassword
            );
    }


    /**
     * Check - shipping substep is complete or not
     *
     * @return boolean
     */
    protected function isShippingCompleted()
    {
        return !$this->isShippingEnabled()
            || (
                $this->isShippingAddressCompleted()
                && $this->isModifierCompleted()
            );
    }

    /**
     * Check modifier
     *
     * @return boolean
     */
    protected function isModifierCompleted()
    {
        return !$this->getModifier()
            || !$this->getModifier()->canApply()
            || $this->getModifier()->getMethod();
    }

    /**
     * Check if payment substep is completed
     *
     * @return boolean
     */
    protected function isPaymentCompleted()
    {
        return $this->getCart()->getProfile()
                && $this->getCart()->getProfile()->getBillingAddress()
                && $this->getCart()->getProfile()->getBillingAddress()->isCompleted(\XLite\Model\Address::BILLING)
                && ($this->getCart()->getPaymentMethod() || $this->isPayedCart());
    }

    /**
     * Check - shipping system is enabled or not
     *
     * @return boolean
     */
    protected function isShippingEnabled()
    {
        return $this->getModifier() && $this->getModifier()->canApply();
    }

    /**
     * Check - payment system is enabled or not
     *
     * @return boolean
     */
    protected function isPaymentEnabled()
    {
        return true;
    }

    // {{{ Shipping section

    /**
     * Check - shipping address is completed or not
     *
     * @return boolean
     */
    protected function isShippingAddressCompleted()
    {
        $profile = $this->getCart()->getProfile();

        return $profile
            && $profile->getShippingAddress()
            && $profile->getShippingAddress()->isCompleted(\XLite\Model\Address::SHIPPING);
    }

    /**
     * Check - shipping rates is available or not
     *
     * @return boolean
     */
    protected function isShippingAvailable()
    {
        return $this->isShippingEnabled();
    }

    /**
     * Get rate markup
     *
     * @param \XLite\Model\Shipping\Rate $rate Shipping rate
     *
     * @return float
     */
    protected function getTotalRate(\XLite\Model\Shipping\Rate $rate)
    {
        return $rate->getTotalRate();
    }

    /**
     * Get modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    protected function getModifier()
    {
        if (null === $this->modifier) {
            $this->modifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        }

        return $this->modifier;
    }

    // }}}

    // {{{ Payment section

    /**
     * Check - billing address is completed or not
     *
     * @return boolean
     */
    public function isBillingAddressCompleted()
    {
        $profile = $this->getCart()->getProfile();

        return $profile
            && $profile->getBillingAddress()
            && $profile->getBillingAddress()->isCompleted(\XLite\Model\Address::BILLING);
    }

    /**
     * Check - shipping and billing addrsses are same or not
     *
     * @return boolean
     */
    public function isSameAddress()
    {
        return !$this->getCart()->getProfile() || $this->getCart()->getProfile()->isEqualAddress();
    }

    /**
     * Return flag if the cart has been already payed
     *
     * @return boolean
     */
    protected function isPayedCart()
    {
        if (!isset($this->isPayedCart)) {
            $this->isPayedCart = $this->getCart()->isPayed();
        }

        return $this->isPayedCart;
    }

    // }}}
}
