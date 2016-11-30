<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View;

/**
 * Product price
 */
class Price extends \XLite\View\Price implements \XLite\Base\IDecorator
{
    /**
     * Widget parameter names
     */
    const PARAM_QUANTITY = 'quantity';

    /**
     * Return JS files for widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/CDev/Wholesale/wholesale_product_page.js';

        return $list;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        $product = parent::getProduct();
        $product->setWholesaleQuantity($this->getParam(static::PARAM_QUANTITY));

        return $product;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_QUANTITY => new \XLite\Model\WidgetParam\TypeInt('Product quantity', 1),
        ];
    }
}
