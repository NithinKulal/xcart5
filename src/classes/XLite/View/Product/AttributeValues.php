<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Product attribute values
 */
class AttributeValues extends \XLite\View\AView
{
    use ExecuteCachedTrait;

    /**
     * Widget param names
     */
    const PARAM_ORDER_ITEM = 'orderItem';
    const PARAM_PRODUCT    = 'product';
    const PARAM_IDX        = 'idx';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'product/details/parts/attributes_modify';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getAttributes();
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ORDER_ITEM => new \XLite\Model\WidgetParam\TypeObject(
                'Order item', null, false, '\XLite\Model\OrderItem'
            ),
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product', null, false, '\XLite\Model\Product'
            ),
            self::PARAM_IDX => new \XLite\Model\WidgetParam\TypeInt(
                'Index of order item', 0, false
            ),
        );
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        $orderItem = $this->getParam(static::PARAM_ORDER_ITEM);

        return $orderItem
            ? $orderItem->getProduct()
            : ($this->getParam(static::PARAM_PRODUCT) ?: \XLite::getController()->getProduct());
    }

    /**
     * Define attributes
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return $this->getProduct()->getEditableAttributes();
    }

    /**
     * Get attributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        return $this->executeCachedRuntime(function () {
            return $this->defineAttributes();
        }, ['getAttributes', $this->getProduct()->getProductId()]);
    }

    /**
     * Get order item index
     *
     * @return integer
     */
    protected function getIdx()
    {
        return $this->getParam(static::PARAM_IDX);
    }

    /**
     * Get order item index
     *
     * @return integer
     */
    protected function getCommonFieldName()
    {
        return 0 < $this->getParam(static::PARAM_IDX)
            ? 'order_items'
            : 'new';
    }
    
    /**
     * Return specific CSS class for attribute wrapper(default <li>)
     *
     * @param $attribute \XLite\Model\Attribute
     *
     * @return string
     */
    protected function getAttributeCSSClass($attribute)
    {
        return '';
    }
}
