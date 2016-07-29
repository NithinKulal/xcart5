<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Checkout;

/**
 * Shipping methods list
 */
class ShippingMethodsList extends \XLite\View\AView
{
    const PARAM_CART = 'cart';

    /**
     * Modifier (cache)
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $modifier;

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'checkout/steps/shipping/parts/shippingMethods.js';
        $list[] = 'form_field/js/rich.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/chosen.jquery.js';
        $list[static::RESOURCE_CSS][] = 'css/chosen/chosen.css';

        return $list;
    }

    /**
     * Check - shipping rates is available or not
     *
     * @return boolean
     */
    public function isShippingAvailable()
    {
        return $this->getModifier()->isRatesExists() && $this->getCart()->getProfile();
    }

    /**
     * Check - is order shippable or not
     *
     * @return boolean
     */
    public function isShippingNeeded()
    {
        return $this->getModifier() && $this->getModifier()->canApply();
    }

    /**
     * No shippings methods available notification
     *
     * @return string
     */
    protected function getShippingNotAvailableNotification()
    {
        return static::t('There are no shipping methods available');
    }

    /**
     * Error message for JS event
     *
     * @return string
     */
    protected function getErrorMessage()
    {
        return static::t('Order cannot be placed because there is no shipping methods available.');
    }

    /**
     * Check - shipping address is completed or not
     *
     * @return boolean
     */
    public function isAddressCompleted()
    {
        $profile = $this->getCart()->getProfile();

        return $profile
            && $profile->getShippingAddress()
            && $profile->getShippingAddress()
                ->isCompleted(\XLite\Model\Address::SHIPPING);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_CART => new \XLite\Model\WidgetParam\TypeObject('Cart', null, false, '\XLite\Model\Cart'),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/steps/shipping/parts/shippingMethodsList.twig';
    }

    /**
     * Get modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    protected function getModifier()
    {
        if (!isset($this->modifier)) {
            $this->modifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        }

        return $this->modifier;
    }

    /**
     * Returns cart
     *
     * @return \XLite\Model\Cart
     */
    protected function getCart()
    {
        return $this->getParam(static::PARAM_CART) ?: \XLite::getController()->getCart();
    }
}
