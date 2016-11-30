<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

/**
 * Tabs related to taxes settings
 *
 * @ListChild (list="admin.center", zone="admin", weight="20")
 */
class Taxes extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'tax_classes';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'tax_classes' => [
                'weight'   => 100,
                'title'    => static::t('Tax classes'),
                'template' => 'tax_classes/body.twig',
            ],
        ];
    }

    /**
     * Checks whether the widget is visible, or not
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && \XLite\Controller\Admin\TaxClasses::isEnabled();
    }
}
