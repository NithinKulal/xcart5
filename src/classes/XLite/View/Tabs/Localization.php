<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to localization
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Localization extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'units_formats';
        $list[] = 'currency';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'units_formats' => [
                'weight'   => 100,
                'title'    => static::t('Units & Formats'),
                'template' => 'settings/body.twig',
            ],
            'currency' => [
                'weight'   => 200,
                'title'    => static::t('Currency'),
                'template' => 'currency.twig',
            ],
        ];
    }
}
