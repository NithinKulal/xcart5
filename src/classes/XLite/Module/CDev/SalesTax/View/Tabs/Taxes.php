<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SalesTax\View\Tabs;

/**
 * Tabs related to taxes settings
 */
abstract class Taxes extends \XLite\View\Tabs\Taxes implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'sales_tax';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        $list['sales_tax'] = [
            'weight'   => 50,
            'title'    => static::t('Sales tax'),
            'template' => 'modules/CDev/SalesTax/body.twig'
        ];

        return $list;
    }
}
