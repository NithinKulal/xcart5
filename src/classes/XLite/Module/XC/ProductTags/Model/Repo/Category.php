<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\Model\Repo;

use XLite\Core\Database;

/**
 * The Product model repository
 *
 * @Decorator\Depend ("XC\ProductFilter")
 */
class Category extends \XLite\Model\Repo\Category implements \XLite\Base\IDecorator
{

    protected function removeProductFilterCacheById($id)
    {
        parent::removeProductFilterCacheById($id);

        \XLite\Core\Database::getCacheDriver()->delete('ProductFilter_Category_Tags_' . $id);
    }
}
