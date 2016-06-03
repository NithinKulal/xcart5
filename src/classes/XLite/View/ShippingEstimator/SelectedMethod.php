<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ShippingEstimator;

/**
 * Selected shipping method view
 */
class SelectedMethod extends \XLite\View\AView
{
    /**
     * Widget parameters
     */
    const PARAM_CART = 'cart';

    /**
     * Shipping modifier
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $modifier;

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_CART => new \XLite\Model\WidgetParam\TypeObject(
                'Cart',
                $this->getDefaultCart(),
                false,
                'XLite\Model\Cart'
            ),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'shopping_cart/parts/box.estimator.method.twig';
    }

    /**
     * Returns default cart value
     *
     * @return \XLite\Model\Cart
     */
    protected function getDefaultCart()
    {
        return \XLite::getController()->getCart();
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

    /**
     * Returns method name
     *
     * @return string
     */
    protected function getName()
    {
        return $this->getModifier()->getMethod()->getName();
    }

    /**
     * Get shipping cost
     *
     * @return float
     */
    protected function getCost()
    {
        $cart = $this->getCart();
        $cost = $cart->getSurchargesSubtotal(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, false);

        return static::formatPrice($cost, $cart->getCurrency(), !\XLite::isAdminZone());
    }

    /**
     * Returns current cart
     *
     * @return \XLite\Model\Cart
     */
    protected function getCart()
    {
        return $this->getParam(static::PARAM_CART);
    }

    protected function isVisible()
    {
        $hasMethod = $this->getModifier()
            && $this->getModifier()->getMethod();

        return parent::isVisible() && $hasMethod;
    }
}
