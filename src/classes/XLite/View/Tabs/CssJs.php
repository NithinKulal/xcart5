<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to look & feel
 * todo: rename
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class CssJs extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'layout';
        $list[] = 'images';
        $list[] = 'css_js_performance';

        return $list;
    }

    /**
     * @todo: rename 'css_js_performance' to '...'
     *
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'layout' => [
                'weight'   => 90,
                'title'    => static::t('Layout'),
                'template' => 'layout_settings/body.twig',
                'cssFiles' => [
                    'layout_settings/style.css',
                ]
            ],
            'images' => [
                'weight'   => 9000,
                'title'    => static::t('Images'),
                'template' => 'images_settings/body.twig',
                'cssFiles' => [
                    'images_settings/style.css',
                ]
            ],
            'css_js_performance' => [
                'weight'   => 10000,
                'title'    => static::t('Performance'),
                'template' => 'performance/body.twig',
            ],
        ];
    }
}
