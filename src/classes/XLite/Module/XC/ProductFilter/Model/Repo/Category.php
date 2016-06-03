<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Model\Repo;

/**
 * The "category" model repository
 *
 */
abstract class Category extends \XLite\Model\Repo\Category implements \XLite\Base\IDecorator
{
    /**
     * Remove product filter cache
     *
     * @param array $ids IDs OPTIONAL
     *
     * @return void
     */
    public function removeProductFilterCache($ids = null)
    {
        if ($ids) {
            foreach ($ids as $id) {
                $this->removeProductFilterCacheById($id);
            }

        } else {
            $categories = $this->createPureQueryBuilder('c')
                ->select('c.category_id')
                ->getQuery()->getScalarResult();

            foreach ($categories as $v) {
                $this->removeProductFilterCacheById($v['category_id']);
            }
        }
    }

    protected function removeProductFilterCacheById($id)
    {
        \XLite\Core\Database::getCacheDriver()->delete('ProductFilter_Category_Attributes_' . $id);
    }
}
