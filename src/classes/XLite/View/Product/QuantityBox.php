<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

/**
 * QuantityBox
 */
class QuantityBox extends \XLite\View\Product\AProduct
{
    /**
     * Widget param names
     */

    const PARAM_PRODUCT      = 'product';
    const PARAM_ORDER_ITEM   = 'orderItem';
    const PARAM_FIELD_NAME   = 'fieldName';
    const PARAM_FIELD_VALUE  = 'fieldValue';
    const PARAM_FIELD_TITLE  = 'fieldTitle';
    const PARAM_STYLE        = 'style';
    const PARAM_IS_CART_PAGE = 'isCartPage';
    const PARAM_FORCE_VALUE  = 'forceValue';
    const PARAM_MAX_VALUE    = 'maxValue';
    const PARAM_MOUSE_WHEEL_CTRL = 'mouseWheelCtrl';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/quantity_box.css';

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

        return $list;
    }


    /**
     * Return directory contains the template
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/quantity_box';
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
            static::PARAM_FIELD_NAME   => new \XLite\Model\WidgetParam\TypeString('Name', 'amount'),
            static::PARAM_FIELD_TITLE  => new \XLite\Model\WidgetParam\TypeString('Title', 'Quantity'),
            static::PARAM_PRODUCT      => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, '\XLite\Model\Product'),
            static::PARAM_ORDER_ITEM   => new \XLite\Model\WidgetParam\TypeObject('Order item', null, false, '\XLite\Model\OrderItem'),
            static::PARAM_FIELD_VALUE  => new \XLite\Model\WidgetParam\TypeInt('Value', null),
            static::PARAM_STYLE        => new \XLite\Model\WidgetParam\TypeString('CSS class', ''),
            static::PARAM_IS_CART_PAGE => new \XLite\Model\WidgetParam\TypeBool('Is cart page', false),
            static::PARAM_FORCE_VALUE  => new \XLite\Model\WidgetParam\TypeBool('Force field value', false),
            static::PARAM_MAX_VALUE    => new \XLite\Model\WidgetParam\TypeInt('Max value', null),
            static::PARAM_MOUSE_WHEEL_CTRL => new \XLite\Model\WidgetParam\TypeBool('Mouse wheel control', $this->getDefaultMouseWheelCtrlValue()),
        );
    }

    /**
     * Get default value for mouseWheelCtrl parameter
     *
     * @return boolean
     */
    protected function getDefaultMouseWheelCtrlValue()
    {
        return false;
    }

    /**
     * Alias
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getOrderItem()
            ? $this->getOrderItem()->getProduct()
            : $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * Alias
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getOrderItem()
    {
        return $this->getParam(static::PARAM_ORDER_ITEM);
    }

    /**
     * Alias
    *
     * @return string
     */
    protected function getBoxName()
    {
        return $this->getParam(static::PARAM_FIELD_NAME);
    }

    /**
     * Alias
     *
     * @return string
     */
    protected function getBoxId()
    {
        return $this->getBoxName() . $this->getProduct()->getProductId();
    }

    /**
     * Alias
     *
     * @return integer
     */
    protected function getBoxValue()
    {
        $value = $this->getParam(static::PARAM_FIELD_VALUE) ?: $this->getProduct()->getMinPurchaseLimit();

        return $this->isCartPage() ? $value : max($value, $this->getMinQuantity());
    }

    /**
     * Alias
     *
     * @return string
     */
    protected function getBoxTitle()
    {
        return $this->getParam(static::PARAM_FIELD_TITLE);
    }

    /**
     * Alias
     *
     * @return boolean
     */
    protected function isCartPage()
    {
        return $this->getParam(static::PARAM_IS_CART_PAGE);
    }

    /**
     * CSS class
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(
            'quantity'
            . ($this->getParam(static::PARAM_MOUSE_WHEEL_CTRL) ? ' wheel-ctrl' : '')
            . ($this->isCartPage() ? ' watcher' : '')
            . ' ' . $this->getParam(static::PARAM_STYLE)
            . ' validate[required,custom[integer],min[' . $this->getMinQuantity() . ']'
            . $this->getAdditionalValidate()
            . ']'
        );
    }

    /**
     * Return additional validate
     *
     * @return string
     */
    protected function getAdditionalValidate()
    {
        return $this->getProduct()->getInventoryEnabled() ? ',max[' . $this->getMaxQuantity() . ']' : '';
    }

    /**
     * Return maximum allowed quantity
     *
     * @return integer
     */
    protected function getMaxQuantity()
    {
        $maxValue = $this->getParam(static::PARAM_MAX_VALUE);

        $orderItemsAmount = $this->getOrderItem()
            ? min(
                $this->getOrderItem()->getAmount(),
                $this->getProduct()->getPublicAmount()
            )
            : 0;

        return isset($maxValue)
            ? $maxValue
            : $this->getProduct()->getAvailableAmount() + $orderItemsAmount;
    }

    /**
     * Return minimum quantity
     *
     * @return integer
     */
    protected function getMinQuantity()
    {
        return 1;
    }
}
