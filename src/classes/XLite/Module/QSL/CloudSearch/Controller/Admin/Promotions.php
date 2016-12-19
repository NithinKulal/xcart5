<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Controller\Admin;

use XLite\Module\QSL\CloudSearch\Main;

/**
 * CloudSearch dashboard page controller (Promotion section)
 */
class Promotions extends \XLite\Controller\Admin\Promotions implements \XLite\Base\IDecorator
{
    /**
     * Page key
     */
    const PAGE_CLOUD_SEARCH_DASHBOARD  = 'cloud_search';
    const PAGE_CLOUD_FILTERS_DASHBOARD = 'cloud_filters';


    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list = parent::getPagesStatic();

        $list[static::PAGE_CLOUD_SEARCH_DASHBOARD] = array(
            'name' => static::t('CloudSearch'),
            'tpl'  => 'modules/QSL/CloudSearch/cloud_search_promotions_menu_body.twig',
        );

        if (Main::isCloudFiltersEnabled()) {
            $list[static::PAGE_CLOUD_FILTERS_DASHBOARD] = array(
                'name' => static::t('CloudFilters'),
                'tpl'  => 'modules/QSL/CloudSearch/cloud_filters_promotions_menu_body.twig',
            );
        }

        return $list;
    }
}
