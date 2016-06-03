<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\Button;

/**
 * Return products button widget
 */
class PopupReturnProducts extends \XLite\View\Button\APopupButton
{
    /**
     * Additional parameters
     */
    const PARAM_ORDER_ID = 'orderId';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/CanadaPost/button/js/popup_return_products.js';

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
        
        // Include create return page widget styles to show the page properly in the popup window
        $list[] = 'modules/XC/CanadaPost/products_return/create/style.css';

        return $list;
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array(
            'target'   => 'capost_returns',
            'widget'   => '\XLite\Module\XC\CanadaPost\View\ReturnProducts',
            'order_id' => $this->getOrderId(),
        );
    }

    /**
     * GEt default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return static::t('Return products');
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
            static::PARAM_ORDER_ID => new \XLite\Model\WidgetParam\TypeInt('Order ID', 0),
        );
    }
    
    /**
     * Get order ID
     *
     * @return integer
     */
    protected function getOrderId()
    {
        return $this->getParam(static::PARAM_ORDER_ID);
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass() . ' capost-return-products-button ' . ($this->getParam(self::PARAM_STYLE) ?: ''));
    }
}
