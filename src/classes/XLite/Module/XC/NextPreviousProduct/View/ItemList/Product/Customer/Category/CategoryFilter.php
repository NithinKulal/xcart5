<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NextPreviousProduct\View\ItemList\Product\Customer\Category;

/**
 * Decorated CategoryFilter items list
 *
 * @Decorator\Depend ("XC\ProductFilter")
 */
abstract class CategoryFilter extends \XLite\Module\XC\ProductFilter\View\ItemsList\Product\Customer\Category\CategoryFilter implements \XLite\Base\IDecorator
{

    /**
     * Define data for getDataString() method
     *
     * @return array
     */
    protected function defineDataForDataString()
    {
        $list = parent::defineDataForDataString();

        $list['parameters']['category_id'] = $this->getCategoryId();

        return $list;
    }
}
