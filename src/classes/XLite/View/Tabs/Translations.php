<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to translations section
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Translations extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'languages';
        $list[] = 'labels';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'languages' => [
                'weight' => 100,
                'title'  => static::t('Languages'),
                'widget' => '\XLite\View\LanguagesModify\Languages',
            ],
            'labels' => [
                'weight' => 200,
                'title'  => static::t('Edit labels'),
                'widget' => '\XLite\View\LanguagesModify\Labels',
            ],
        ];
    }
}
