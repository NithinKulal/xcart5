<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\ItemsList\Model;

/**
 * Categories items list
 */
class Category extends \XLite\View\ItemsList\Model\Category implements \XLite\Base\IDecorator
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/CDev/FeaturedProducts/items_list/category/style.css';

        return $list;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array_merge(parent::defineColumns(), [
            'featured_products' => [
                static::COLUMN_NAME     => static::t('Featured'),
                static::COLUMN_TEMPLATE => false,
                static::COLUMN_ORDERBY  => 250,
            ],
        ]);
    }
}
