<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Controller\Admin;

/**
 * Product page controller
 */
class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * Update attributes
     *
     * @return void
     */
    protected function doActionUpdateAttributes()
    {
        $categoriesToRemoveCache = array();
        foreach ($this->getProduct()->getCategoryProducts()->toArray() as $cp) {
            $categoriesToRemoveCache[] = $cp->getCategory()->getCategoryId();
        }

        if ($categoriesToRemoveCache) {
            \XLite\Core\Database::getRepo('XLite\Model\Category')->removeProductFilterCache(
                $categoriesToRemoveCache
            );
        }

        parent::doActionUpdateAttributes();
    }
}
