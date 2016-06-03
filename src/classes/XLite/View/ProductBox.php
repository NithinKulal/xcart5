<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Product box widget
 */
class ProductBox extends \XLite\View\SideBarBox
{
    /**
     * Widget parameter names
     */
    const PARAM_PRODUCT_ID      = 'product_id';
    const PARAM_ICON_MAX_WIDTH  = 'iconWidth';
    const PARAM_ICON_MAX_HEIGHT = 'iconHeight';


    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $result = parent::getCSSFiles();

        $result[] = 'items_list/product/products_list.css';
        $result[] = 'product_box/style.css';

        return $result;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $result = parent::getJSFiles();

        $result[] = 'product_box/controller.js';

        return $result;
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Product';
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'product_box';
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->widgetParams[self::PARAM_PRODUCT_ID]->getObject();
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
            self::PARAM_PRODUCT_ID => new \XLite\Model\WidgetParam\ObjectId\Product('Product Id', 0, true),
            self::PARAM_ICON_MAX_WIDTH => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon width', 160, true
            ),
            self::PARAM_ICON_MAX_HEIGHT => new \XLite\Model\WidgetParam\TypeInt(
                'Maximal icon height', 160, true
            ),
        );
    }

    /**
     * getIconWidth
     *
     * @return integer
     */
    protected function getIconWidth()
    {
        return $this->getParam(self::PARAM_ICON_MAX_WIDTH);
    }

    /**
     * getIconHeight
     *
     * @return integer
     */
    protected function getIconHeight()
    {
        return $this->getParam(self::PARAM_ICON_MAX_HEIGHT);
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getProduct()->isAvailable();
    }

}
