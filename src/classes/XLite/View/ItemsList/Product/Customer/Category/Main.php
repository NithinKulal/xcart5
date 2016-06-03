<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Product\Customer\Category;

use XLite\View\CacheableTrait;

/**
 * Category products list widget
 *
 * @ListChild (list="center.bottom", zone="customer", weight="200")
 */
class Main extends \XLite\View\ItemsList\Product\Customer\Category\ACategory
{
    use CacheableTrait;

    /**
     * Return name of the session cell identifier
     *
     * @return string
     */
    public function getSessionCell()
    {
        return parent::getSessionCell() . \XLite\Core\Request::getInstance()->category_id;
    }
}
