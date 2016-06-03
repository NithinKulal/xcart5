<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\View\Model;

/**
 * Decorate product settings page
 */
class Product extends \XLite\View\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Update product categories
     *
     * @param \XLite\Model\Product $model       Product model
     * @param array                $categoryIds List of IDs of new categories
     *
     * @return void
     */
    protected function updateProductCategories($model, $categoryIds)
    {
        $categoriesToRemoveCache = array();

        // List of old category IDs
        $oldCategoryIds = array();

        // Get old category IDs list
        $oldCategoryProducts = $model->getCategoryProducts()->toArray();

        if (!empty($oldCategoryProducts)) {

            foreach ($oldCategoryProducts as $cp) {
                $oldCategoryIds[] = $cp->getCategory()->getCategoryId();

                if (!in_array($cp->getCategory()->getCategoryId(), $categoryIds)) {
                    $categoriesToRemoveCache[] = $cp->getCategory()->getCategoryId();
                }
            }

        }

        $categoriesToRemoveCache = array_merge(
            $categoriesToRemoveCache,
            array_diff($categoryIds, $oldCategoryIds)
        );

        if ($categoriesToRemoveCache) {
            \XLite\Core\Database::getRepo('XLite\Model\Category')->removeProductFilterCache(
                $categoriesToRemoveCache
            );
        }

        parent::updateProductCategories($model, $categoryIds);
    }
}
