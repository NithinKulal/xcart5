<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product;

use XLite\Core\View\DynamicWidgetInterface;
use XLite\Model\WidgetParam\TypeInt;

/**
 * ProductAddedToCartCellClass dynamic widget renders 'product-added' css class on a product if it is contained in a customer's cart.
 */
class ProductAddedToCartCellClass extends \XLite\View\AView implements DynamicWidgetInterface
{
    const PARAM_PRODUCT_ID = 'product_id';

    /**
     * Display widget with the default or overriden template.
     *
     * @param $template
     */
    protected function doDisplay($template = null)
    {
        if ($this->getCart()->isProductAdded($this->getParam(static::PARAM_PRODUCT_ID))) {
            echo 'product-added';
        }
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
            static::PARAM_PRODUCT_ID => new TypeInt('ProductId'),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }
}
