<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\NextPreviousProduct\View\Product;

use XLite\Model\WidgetParam\TypeInt;
use XLite\Model\WidgetParam\TypeString;

/**
 * Product list item widget
 */
class ListItem extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Widget parameters
     */
    const PARAM_PAGE_ID          = 'pageId';
    const PARAM_POSITION_ON_PAGE = 'positionOnPage';
    const PARAM_ITEM_LIST_CLASS  = 'itemListClass';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PAGE_ID          => new TypeInt('Page id'),
            self::PARAM_POSITION_ON_PAGE => new TypeInt('Position on page'),
            self::PARAM_ITEM_LIST_CLASS  => new TypeString('Item list class'),
        ];
    }

    /**
     * Return class attribute for the product cell
     *
     * @return string
     */
    public function getProductCellClass()
    {
        $result = parent::getProductCellClass();

        $disabledLists = [
            'XLite\Module\XC\Add2CartPopup\View\Products'
        ];

        if (in_array($this->getParam(self::PARAM_ITEM_LIST_CLASS), $disabledLists, true)) {
            $result .= ' next-previous-disabled';
        }

        return $this->getSafeValue($result);
    }

    /**
     * json string for data attribute
     *
     * @return string
     */
    protected function getDataString()
    {
        return json_encode($this->defineDataForDataString());
    }

    /**
     * Define data for getDataString() method
     *
     * @return array
     */
    protected function defineDataForDataString()
    {
        return [
            'class'      => $this->getParam(self::PARAM_ITEM_LIST_CLASS),
            'pageId'     => $this->getParam(self::PARAM_PAGE_ID),
            'position'   => $this->getParam(self::PARAM_POSITION_ON_PAGE),
            'parameters' => [],
        ];
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();
        $params[] = $this->getDataString();

        return $params;
    }
}