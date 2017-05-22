<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductComparison\View\ComparisonTable;

/**
 * Attributes
 *
 * @ListChild (list="comparison_table.data", weight="300")
 */
class Attributes extends \XLite\Module\XC\ProductComparison\View\ComparisonTable\AComparisonTable
{
    /**
     * Product classes
     *
     * @var array
     */
    protected $productClasses;

    /**
     * Get active product classes
     *
     * @return array
     */
    protected function getProductClasses()
    {
        if (!isset($this->productClasses)) {
            $cnd = new \XLite\Core\CommonCell();
            $cnd->product = $this->getProducts();
            $this->productClasses = \XLite\Core\Database::getRepo('\XLite\Model\ProductClass')->search($cnd);
        }

        return $this->productClasses;
    }

    /**
     * Get global groups
     *
     * @return mixed
     */
    protected function getGlobalGroups()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->findByProductClass(null);
    }

    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/parts/attributes';
    }
}
