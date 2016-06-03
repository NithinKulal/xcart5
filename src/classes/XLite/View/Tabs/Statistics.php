<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to user profile section
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Statistics extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'orders_stats';
        $list[] = 'top_sellers';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'orders_stats' => [
                'weight' => 100,
                'title'  => static::t('Order statistics'),
            ],
            'top_sellers' => [
                'weight' => 200,
                'title'  => static::t('Top sellers'),
            ],
        ];
    }
}
