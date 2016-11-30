<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\ItemsList\Product\Customer;

/**
 * ACustomer
 * @Decorator\Before({"XC\Reviews", "CDev\Bestsellers"})
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer implements \XLite\Base\IDecorator
{
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        $this->sortOrderModes[self::SORT_BY_MODE_NAME] = [
            self::SORT_ORDER_ASC => static::t('A - Z'),
            self::SORT_ORDER_DESC => static::t('Z - A')
        ];
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = [
            'file'  => 'css/less/cart-tray.less',
            'media' =>  'screen',
            'merge' =>  'bootstrap/css/bootstrap.less',
        ];

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'js/add_to_cart.js';
        $list[] = 'modules/XC/CrispWhiteSkin/items_list/product/products_list.js';

        return $list;
    }

    /**
     * Get display mode link class name
     * TODO - simplify
     *
     * @param string $displayMode Display mode
     *
     * @return string
     */
    protected function getDisplayModeCSS($displayMode)
    {
        $classes = array(
            $displayMode
        );

        switch ($displayMode) {
            case static::DISPLAY_MODE_GRID:
                $icon = 'icon-grid-view';
                break;

            case static::DISPLAY_MODE_LIST:
                $icon = 'icon-list-view';
                break;

            case static::DISPLAY_MODE_TABLE:
                $icon = 'icon-table-view';
                break;
        }

        $classes[] = $icon;

        return implode(' ', $classes);
    }

    /**
     * @return string
     */
    protected function getSortOrderLabel($key = null)
    {
        if (!$key || $key === $this->getSortBy()) {

            if (isset($this->sortOrderModes[$this->getSortBy()])) {
                return $this->getSortArrowClassCSS($this->getSortBy()) != ''
                    ? $this->sortOrderModes[$this->getSortBy()][$this->getSortOrder()]
                    : '';
            }

            return $this->getSortArrowClassCSS($this->getSortBy()) != ''
                ? $this->sortOrderModes[$this->getSortOrder()]
                : '';
        }

        return '';
    }

    /**
     * @return string
     */
    protected function getAscOrderLabel($key = null)
    {
        if (!is_null($key) && isset($this->sortOrderModes[$key])) {
            return $this->sortOrderModes[$key][self::SORT_ORDER_ASC];
        }

        return $this->sortOrderModes[self::SORT_ORDER_ASC];
    }

    /**
     * @return string
     */
    protected function getDescOrderLabel($key = null)
    {
        if (!is_null($key) && isset($this->sortOrderModes[$key])) {
            return $this->sortOrderModes[$key][self::SORT_ORDER_DESC];
        }

        return $this->sortOrderModes[self::SORT_ORDER_DESC];
    }

    /**
     * isSortByModeSelected
     *
     * @param string $sortByMode Value to check
     *
     * @return boolean
     */
    protected function isSortByModeSelected($sortByMode, $order = null)
    {
        return parent::isSortByModeSelected($sortByMode) && (is_null($order) || $order == $this->getSortOrder());
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        $sizes = static::getIconSizes();
        $key = $this->getWidgetType() . '.' . $this->getParam(static::PARAM_DISPLAY_MODE);
        $size = isset($sizes[$key]) ? $sizes[$key] : $sizes['other'];

        $this->widgetParams[static::PARAM_ICON_MAX_WIDTH]->setValue($size[0]);
        $this->widgetParams[static::PARAM_ICON_MAX_HEIGHT]->setValue($size[1]);
    }
}
