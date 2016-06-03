<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Logic\Export\Step;

/**
 * Categories
 */
abstract class Categories extends \XLite\Logic\Export\Step\Categories implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['productClasses']  = array();
        $columns['useClasses']      = array();

        return $columns;
    }

    /**
     * Get column value for 'productClasses' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getProductClassesColumnValue(array $dataset, $name, $i)
    {
        $result = array();

        foreach ($dataset['model']->getProductClasses() as $productClass) {
            $result[] = $productClass->getName();
        }

        return $result;
    }

    /**
     * Get column value for 'useClasses' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getUseClassesColumnValue(array $dataset, $name, $i)
    {
        return $this->getColumnValueByName($dataset['model'], 'useClasses');
    }
}
