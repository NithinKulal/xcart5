<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Model;

/**
 * OrderItem selector widget
 */
class OrderItemSelector extends \XLite\View\FormField\Select\Model\ProductSelector
{
    const PARAM_ORDER_ID = 'order_id';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ORDER_ID => new \XLite\Model\WidgetParam\TypeInt('Order id', 0),
        );

        $this->widgetParams[static::PARAM_PLACEHOLDER]->setValue(static::t('Enter product name or SKU'));
    }

    /**
     * Defines the name of the text value input
     *
     * @return string
     */
    protected function getTextName()
    {
        $name = $this->getParam(static::PARAM_NAME);
        $newName = preg_replace('/^(.+)\[(\w+)\]$/', '\\1[\\2_text]', $name);

        return $newName === $name ? $name . '_text' : $newName;
    }

    /**
     * Defines the URL to request the models
     *
     * @return string
     */
    protected function getDefaultGetter()
    {
        return $this->buildURL('model_order_item_selector');
    }

    /**
     * Returns getter url
     *
     * @return string
     */
    protected function getGetter()
    {
        return $this->buildURL('model_order_item_selector', '', array('order_id' => $this->getOrderId()));
    }

    /**
     * Returns order id
     *
     * @return integer
     */
    protected function getOrderId()
    {
        return $this->getParam(static::PARAM_ORDER_ID);
    }

    /**
     * Get model defined template
     *
     * @return string
     */
    protected function getModelDefinedTemplate()
    {
        return 'order/page/parts/model_defined.twig';
    }

    /**
     * Register the CSS classes to be set to the widget
     *
     * @return array
     */
    protected function defineCSSClasses()
    {
        $classes = parent::defineCSSClasses();

        $classes[] = 'no-validate';
        $classes[] = 'not-significant';

        return $classes;
    }
}
