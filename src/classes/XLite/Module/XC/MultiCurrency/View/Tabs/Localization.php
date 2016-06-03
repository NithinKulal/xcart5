<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\Tabs;

/**
 * Tabs related to localization
 */
abstract class Localization extends \XLite\View\Tabs\Localization implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = array_diff(parent::getAllowedTargets(), ['currency']);
        $list[] = 'currencies';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        unset($list['currency']);
        $list['currencies'] = [
            'weight'   => 300,
            'title'    => static::t('Currencies'),
            'template' => 'modules/XC/MultiCurrency/currencies.twig',
        ];

        return $list;
    }
}
