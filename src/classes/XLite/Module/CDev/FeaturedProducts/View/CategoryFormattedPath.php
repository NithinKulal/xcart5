<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FeaturedProducts\View;

/**
 * Category formatted path
 */
abstract class CategoryFormattedPath extends \XLite\View\CategoryFormattedPath implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return void
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'featured_products';

        return $list;
    }
}
