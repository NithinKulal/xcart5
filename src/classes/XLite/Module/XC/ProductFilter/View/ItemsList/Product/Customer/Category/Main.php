<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category;

/**
 * Category filters list widget
 */
abstract class Main extends \XLite\View\ItemsList\Product\Customer\Category\Main implements \XLite\Base\IDecorator
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_diff(parent::getAllowedTargets(), array('category'));
    }
}
