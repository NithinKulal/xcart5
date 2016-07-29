<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Blocks;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
class ShippingMethods extends \XLite\View\Checkout\ShippingMethodsList
{
    /**
     * Runtime cache
     */
    protected $modifier;
    protected $rates;

    /**
     * @return string
     */
    public function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/shipping_methods/';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = array();

        $list[] = array(
            'file'  => $this->getDir() . 'style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = array();

        $list[] = $this->getDir() . 'shipping-methods.js';

        return $list;
    }

    /**
     * Get common resources
     *
     * @return array
     */
    protected function getCommonFiles()
    {
        return array();
    }

    /**
     * Get view list name
     * @param  string $field Field name
     * @return string
     */
    public function getListName($field = null)
    {
        $name = 'checkout_fastlane.blocks.shipping_methods';

        if ($field) {
            $name .= '.' . $field;
        }

        return $name;
    }

    /**
     * Check - form is visible or not
     *
     * @return boolean
     */
    protected function isFormVisible()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return 'shipping-methods';
    }

    /**
     * @return void
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'template.twig';
    }

    /**
     * @return string
     */
    public function defineWidgetData()
    {
        return array();
    }

    /**
     * @return string
     */
    public function getWidgetData()
    {
        return json_encode($this->defineWidgetData());
    }

    /**
     * @return string
     */
    protected function getShippingMethodsList()
    {
        $self = $this;
        $list = array_reduce(
            $this->getRates(),
            function ($acc, $rate) use ($self) {
                $acc[$self->getMethodId($rate)] = $self->getMethodName($rate);

                return $acc;
            },
            []
        );

        return json_encode($list);
    }

    /**
     * Get rate method id
     *
     * @param \XLite\Model\Shipping\Rate $rate Shipping rate
     *
     * @return integer
     */
    protected function getMethodId(\XLite\Model\Shipping\Rate $rate)
    {
        return $rate->getMethod()->getMethodId();
    }

    /**
     * Get rate method name
     *
     * @param \XLite\Model\Shipping\Rate $rate Shipping rate
     *
     * @return string
     */
    protected function getMethodName(\XLite\Model\Shipping\Rate $rate)
    {
        return $rate->getMethod()->getName();
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
     * Get shipping rates
     *
     * @return array
     */
    protected function getRates()
    {
        if (!isset($this->rates)) {
            $this->rates = $this->getModifier()->getRates();
        }

        return $this->rates;
    }
}
