<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\NextPreviousProduct\View\ItemList\Product\Customer\Category;

/**
 * Decorated Main items list
 */
abstract class Main extends \XLite\View\ItemsList\Product\Customer\Category\Main implements \XLite\Base\IDecorator
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
