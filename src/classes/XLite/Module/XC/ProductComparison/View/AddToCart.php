<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductComparison\View;

/**
 * Add to cart widget
 *
 */
class AddToCart extends \XLite\View\Dialog
{
    /**
     * Widget parameter names
     */
    const PARAM_PRODUCT = 'product';

    /**
     * Cart amount
     *
     * @var integer
     */
    protected $cartAmount;

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * Get product id
     *
     * @return integer
     */
    protected function getProductId()
    {
        return $this->getProduct()
            ? $this->getProduct()->getProductId()
            : null;
    }

    /**
     * Get cart amount
     *
     * @return integer
     */
    protected function getCartAmount()
    {
        if (!isset($this->cartAmount)) {
            $this->cartAmount = 0;
            foreach ($this->getCart()->getItems() as $item) {
                $product = $item->getProduct();
    
                if ($product && $product->getProductId() == $this->getProductId()) {
                    $this->cartAmount += $item->getAmount(); 
                }
            }

        }

        return $this->cartAmount;
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
            self::PARAM_PRODUCT  => new \XLite\Model\WidgetParam\TypeObject(
                null,
                false,
                'XLite\Model\Product'
            ),
        );
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/ProductComparison/add_to_cart';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

}
