<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View\Tabs;

/**
 * Tabs related to category section
 */
abstract class Category extends \XLite\View\Tabs\Category implements \XLite\Base\IDecorator
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
        if (\XLite\Core\Request::getInstance()->id) {
            $list['featured_products'] = [
                'weight'   => 400,
                'title'    => static::t('Featured products'),
                'template' => 'modules/CDev/FeaturedProducts/featured_products.twig',
            ];
        }

        return $list;
    }
}
