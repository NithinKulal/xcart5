<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Product options list
 */
class SaleDiscountTypes extends \XLite\View\AView
{
    /**
     * Sale price value name
     */
    const PARAM_SALE_PRICE_VALUE = 'salePriceValue';

    /**
     * Discount type name
     */
    const PARAM_DISCOUNT_TYPE    = 'discountType';


    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/CDev/Sale/sale_discount_types/js/script.js';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/Sale/sale_discount_types/css/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Sale/sale_discount_types/body.twig';
    }

    /**
     * Return percent off value.
     *
     * @return integer
     */
    protected function getPercentOffValue()
    {
        return (int) $this->getParam('salePriceValue');
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
            self::PARAM_SALE_PRICE_VALUE => new \XLite\Model\WidgetParam\TypeFloat('Sale price value', 0),
            self::PARAM_DISCOUNT_TYPE    => new \XLite\Model\WidgetParam\TypeString('Discount type', \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT),
        );
    }

}
