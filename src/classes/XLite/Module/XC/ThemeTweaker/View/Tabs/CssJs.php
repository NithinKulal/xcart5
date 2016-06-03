<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Tabs;

/**
 * Tabs related to look & feel
 */
abstract class CssJs extends \XLite\View\Tabs\CssJs implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'theme_tweaker_templates';
        $list[] = 'custom_css';
        $list[] = 'custom_js';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        $list['theme_tweaker_templates'] = [
            'weight'   => 200,
            'title'    => static::t('Webmaster mode'),
            'template' => 'modules/XC/ThemeTweaker/theme_tweaker_templates/body.twig',
        ];
        $list['custom_css'] = [
            'weight'   => 300,
            'title'    => static::t('Custom CSS'),
            'template' => 'modules/XC/ThemeTweaker/custom_css.twig',
        ];
        $list['custom_js'] = [
            'weight'   => 400,
            'title'    => static::t('Custom JavaScript'),
            'template' => 'modules/XC/ThemeTweaker/custom_js.twig',
        ];

        return $list;
    }
}
