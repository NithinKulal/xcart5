<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Product;

/**
 * Abstract product-based form
 */
abstract class AProduct extends \XLite\View\Form\ItemsList\AItemsListSearch
{
    /**
     * Widget parameter names
     */
    const PARAM_PRODUCT = 'product';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product', null, false, '\XLite\Model\Product'
            ),
        );
    }

    /**
     * getProduct
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product_list';
    }

}
