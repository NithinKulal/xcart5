<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\View;

/**
 * 'Add to Cart Popup' page widget
 *
 * @ListChild (list="center")
 */
class Add2Cart extends \XLite\View\Dialog
{
    /**
     * Widget param names
     */
    const PARAM_ICON_MAX_WIDTH  = 'iconWidth';
    const PARAM_ICON_MAX_HEIGHT = 'iconHeight';
    const PARAM_DISPLAY_CACHED  = 'displayCached';

    /**
     * Last order item
     *
     * @var \XLite\Model\OrderItem
     */
    protected static $item = null;


    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'add2_cart_popup';

        return $list;
    }

    /**
     * Display widget
     *
     * @param string $template Template file name OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
        if ($this->getParam(static::PARAM_DISPLAY_CACHED)) {
            if (\XLite\Core\Session::getInstance()->add2CartPopupContent) {
                print \XLite\Core\Session::getInstance()->add2CartPopupContent;
            }

        } else {
            parent::display($template);
        }
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        if (
            !isset($params[static::PARAM_ICON_MAX_WIDTH])
            && !isset($params[static::PARAM_ICON_MAX_HEIGHT])
            && 0 == $this->getParam(static::PARAM_ICON_MAX_WIDTH)
            && 0 == $this->getParam(static::PARAM_ICON_MAX_HEIGHT)
        ) {
            $key = \XLite\View\ItemsList\Product\Customer\ACustomer::WIDGET_TYPE_CENTER
                . '.'
                . \XLite\View\ItemsList\Product\Customer\ACustomer::DISPLAY_MODE_GRID;

            $sizes = \XLite\View\ItemsList\Product\Customer\ACustomer::getIconSizes();
            $size = isset($sizes[$key]) ? $sizes[$key] : $sizes['other'];

            $this->widgetParams[static::PARAM_ICON_MAX_WIDTH]->setValue($size[0]);
            $this->widgetParams[static::PARAM_ICON_MAX_HEIGHT]->setValue($size[1]);
        }
    }


    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Add2CartPopup';
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
            static::PARAM_DISPLAY_CACHED => new \XLite\Model\WidgetParam\TypeBool(
                'Display cached content', true, false
            ),
            static::PARAM_ICON_MAX_WIDTH => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon width', 0, true
            ),
            static::PARAM_ICON_MAX_HEIGHT => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon height', 0, true
            ),
        );
    }

    /**
     * Return the maximal icon width
     *
     * @return integer
     */
    protected function getIconWidth()
    {
        return $this->getParam(static::PARAM_ICON_MAX_WIDTH);
    }

    /**
     * Return the maximal icon height 
     *
     * @return integer
     */
    protected function getIconHeight()
    {
        return $this->getParam(static::PARAM_ICON_MAX_HEIGHT);
    }

    /**
     * Get last item added to cart
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getItem()
    {
        if (!isset(static::$item)) {

            if (\XLite\Core\Session::getInstance()->lastAddedCartItemId) {
                // Try to get item by itemId
                static::$item = $this->getCart()->getItemByItemId(\XLite\Core\Session::getInstance()->lastAddedCartItemId);
            }

            if (!isset(static::$item) && \XLite\Core\Session::getInstance()->lastAddedCartItemKey) {
                // Try to get item by item key
                static::$item = $this->getCart()->getItemByItemKey(\XLite\Core\Session::getInstance()->lastAddedCartItemKey);
            }

            if (!isset(static::$item)) {
                // Try to get last item in the cart

                $items = $this->getCart()->getItems();

                if ($items) {
                    static::$item = $items[count($items) - 1];
                }
            }
        }

        if (!isset(static::$item)) {
            // This is an exceptional situation and should never occur. Just in case...
            static::$item = new \XLite\Model\OrderItem;
        }

        return static::$item;
    }

    /**
     * Get added to cart product object
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getItem() ? $this->getItem()->getProduct() : null;
    }

    /**
     * Return true if products list shoud be displayed in popup
     *
     * @return boolean
     */
    protected function isProductsListEnabled()
    {
        $options = \XLite\Module\XC\Add2CartPopup\Core\Add2CartPopup::getInstance()->getSelectedSourcesOption();

        return !empty($options);
    }

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
        return static::t('Cart contains products with wrong quantity');
    }

    /**
     * Defines the error message if the maximum order amount exceeds
     *
     * @return string
     */
    protected function getMaxOrderAmountErrorReason()
    {
        return static::t(
            'The order subtotal exceeds the maximum allowed value ({{max_order_amount}})',
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
            'The order subtotal less than the minimum allowed value ({{min_order_amount}})',
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
