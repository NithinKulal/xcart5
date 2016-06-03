<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\ItemsList\Product\Customer;

/**
 * ACustomer
 */
abstract class ACustomer extends \XLite\View\ItemsList\Product\Customer\ACustomer implements \XLite\Base\IDecorator
{
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
