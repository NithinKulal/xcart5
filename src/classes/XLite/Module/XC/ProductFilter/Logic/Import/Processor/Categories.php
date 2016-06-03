<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Logic\Import\Processor;

/**
 * Categories
 */
abstract class Categories extends \XLite\Logic\Import\Processor\Categories implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['productClasses']  = array(
            static::COLUMN_IS_MULTIPLE     => true
        );
        $columns['useClasses']      = array();

        return $columns;
    }

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + array(
                'USE-CLASSES-FMT'     => 'Wrong useClasses format',
            );
    }

    /**
     * Verify 'productClasses' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyProductClasses($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsNull($value)) {
            foreach ($value as $productClass) {
                if (!$this->verifyValueAsEmpty($productClass) && !$this->verifyValueAsProductClass($productClass)) {
                    $this->addWarning('GLOBAL-PRODUCT-CLASS-FMT', array('column' => $column, 'value' => $value));
                }
            }
        }
    }

    /**
     * Import 'ProductClasses' value
     *
     * @param \XLite\Model\Category $model  Category
     * @param array                 $value  Value
     * @param array                 $column Column info
     *
     * @return void
     */
    protected function importProductClassesColumn(\XLite\Model\Category $model, array $value, array $column)
    {
        if ($value) {
            if ($model->getProductClasses()) {
                foreach ($model->getProductClasses() as $productClass) {
                    $productClass->getCategories()->removeElement($model);
                }
                $model->getProductClasses()->clear();
            }

            if (!$this->verifyValueAsNull($value)) {
                foreach ($value as $productClass) {
                    $productClass = $this->normalizeValueAsProductClass($productClass);
                    if ($productClass) {
                        $model->addProductClasses($productClass);
                        $productClass->addCategories($model);
                    }
                }
            }
        }
    }

    /**
     * Verify 'useClasses' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyUseClasses($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value) && !$this->verifyValueAsSet(
                $value,
                array_keys(\XLite\Model\Category::getAllowedUseClasses())
            )
        ) {
            $this->addError('USE-CLASSES-FMT', array('column' => $column, 'value' => $value));
        }
    }

}
