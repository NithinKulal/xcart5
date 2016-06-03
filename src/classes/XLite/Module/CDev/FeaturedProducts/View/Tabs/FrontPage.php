<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\Tabs;

/**
 * Tabs related to front page section
 */
abstract class FrontPage extends \XLite\View\Tabs\FrontPage implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'featured_products';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();
        $list['featured_products'] = [
            'weight'   => 300,
            'title'    => static::t('Featured products'),
            'template' => 'modules/CDev/FeaturedProducts/featured_products.twig',
        ];

        return $list;
    }
}
