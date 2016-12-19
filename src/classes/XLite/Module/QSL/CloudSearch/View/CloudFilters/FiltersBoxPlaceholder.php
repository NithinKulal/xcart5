<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\View\CloudFilters;


/**
 * Cloud filters sidebar box widget
 *
 * @ListChild (list="sidebar.single", zone="customer", weight="110")
 * @ListChild (list="sidebar.first", zone="customer", weight="110")
 */
class FiltersBoxPlaceholder extends \XLite\View\AView
{
    const CLOUD_FILTERS_PLACEHOLDER_VALUE = '__CLOUD_FILTERS_PLACEHOLDER_WIDGET__';

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();

        $result[] = 'search';
        $result[] = 'category';

        return $result;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/CloudSearch/cloud_filters/placeholder.twig';
    }

    protected function getPlaceholderValue()
    {
        return self::CLOUD_FILTERS_PLACEHOLDER_VALUE;
    }
}