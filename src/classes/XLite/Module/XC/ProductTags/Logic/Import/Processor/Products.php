<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\Logic\Import\Processor;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['tags'] = array(
            static::COLUMN_IS_MULTIPLE     => true
        );

        return $columns;
    }

    /**
     * Verify 'tags' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyTags($value, array $column)
    {
    }

    /**
     * Import 'marketPrice' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param string               $value  Value
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importTagsColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        // TODO: add verifyValueAsNull()
        if ($model->getTags()) {
            $model->getTags()->clear();
        }

        if ($value) {
            foreach ($value as $index => $tag) {
                if ($tag) {
                    $entity = \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag')->findOneByName($tag);
                    if (!$entity) {
                        $entity = new \XLite\Module\XC\ProductTags\Model\Tag();
                        $entity->setName($tag);
                        \XLite\Core\Database::getRepo('XLite\Module\XC\ProductTags\Model\Tag')->insert($entity);
                    }
                    $model->addTags($entity);
                    $entity->addProducts($model);
                }
            }
        }
    }
}
