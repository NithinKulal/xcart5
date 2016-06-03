<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Cart widget
 *
 * @ListChild (list="center")
 */
class Cart extends \XLite\View\Dialog
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'cart';

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/cart.css';
        $list[] = 'product/details/parts/attributes_modify/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';
        $list[] = 'form_field/js/rich.js';
        $list[] = 'form_field/js/shipping_list.js';

        return $list;
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();

        $list[static::RESOURCE_JS][] = 'js/chosen.jquery.js';

        $list[static::RESOURCE_CSS][] = 'css/chosen/chosen.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'shopping_cart';
    }

    /**
     * Return file name for body template
     *
     * @return void
     */
    protected function getBodyTemplate()
    {
        return $this->getCart()->isEmpty() ? 'empty.twig' : parent::getBodyTemplate();
    }

    /**
     * Get groups of cart items
     *
     * @return array
     */
    protected function getCartItemsGroups()
    {
        $group = array(
            'items' => $this->getCart()->getItems(),
        );

        return array($group);
    }

    // {{{ Surcharges

    /**
     * Get surcharge totals
     *
     * @return array
     */
    protected function getSurchargeTotals()
    {
        return $this->getCart()->getSurchargeTotals();
    }

    /**
     * Get surcharge class name
     *
     * @param string $type      Surcharge type
     * @param array  $surcharge Surcharge
     *
     * @return string
     */
    protected function getSurchargeClassName($type, array $surcharge)
    {
        return 'order-modifier '
            . $type . '-modifier '
            . strtolower($surcharge['code']) . '-code-modifier';
    }

    /**
     * Format surcharge value
     *
     * @param array $surcharge Surcharge
     *
     * @return string
     */
    protected function formatSurcharge(array $surcharge)
    {
        return abs($surcharge['cost']);
    }

    /**
     * Get exclude surcharges by type
     *
     * @param string $type Surcharge type
     *
     * @return array
     */
    protected function getExcludeSurchargesByType($type)
    {
        return $this->getCart()->getExcludeSurchargesByType($type);
    }

    // }}}
    
    /**
     * Get the detailed description of the reason why the cart is disabled
     * 
     * @return string
     */
    protected function getDisabledReason()
    {
        $result = '';

        $cart = $this->getCart();

        if ($cart->isMaxOrderAmountError()) {
            $result = $this->getMaxOrderAmountErrorReason();

        } elseif ($cart->isMinOrderAmountError()) {
            $result = $this->getMinOrderAmountErrorReason();

        } elseif ($cart->getItemsWithWrongAmounts()) {
            $result = $this->getItemsWithWrongAmountErrorReason();
        }

        return $result;
    }

    /**
     * Defines the error message if cart contains products with wrong quantity
     *
     * @return string
     */
    protected function getItemsWithWrongAmountErrorReason()
    {
        return static::t('Cart contains products with wrong quantity. Please correct this to proceed');
    }

    /**
     * Defines the error message if the maximum order amount exceeds
     * 
     * @return string
     */
    protected function getMaxOrderAmountErrorReason()
    {
        return static::t(
            'The order subtotal exceeds the maximum allowed value ({{max_order_amount}}), please remove some items from the cart.',
            array(
                'max_order_amount' => static::formatPrice(
                    \XLite\Core\Config::getInstance()->General->maximal_order_amount,
                    null,
                    true
                ),
            )
        );
    }
    
    /**
     * Defines the error message if the total is less than minimum order amount
     * 
     * @return string
     */
    protected function getMinOrderAmountErrorReason()
    {
        return static::t(
            'The order subtotal less than the minimum allowed value ({{min_order_amount}}), please add some items to the cart.',
            array(
                'min_order_amount' => static::formatPrice(
                    \XLite\Core\Config::getInstance()->General->minimal_order_amount,
                    null,
                    true
                ),
            )
        );
    }
}
