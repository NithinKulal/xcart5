<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Logic\Export\Step;

/**
 * Products
 */
abstract class Products extends \XLite\Logic\Export\Step\Products implements \XLite\Base\IDecorator
{
    const VARIANT_PREFIX = 'variant';

    // {{{ Data

    /**
     * Get model datasets
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return array
     */
    protected function getModelDatasets(\XLite\Model\AEntity $model)
    {
        $result = parent::getModelDatasets($model);

        if ('none' !== $this->generator->getOptions()->attrs) {
            $result = $this->distributeDatasetModel(
                $result,
                'variant',
                $model->getVariants()
            );
        }

        return $result;
    }

    // }}}

    // {{{ Columns

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        if ('none' !== $this->generator->getOptions()->attrs) {
            $columns += array(
                static::VARIANT_PREFIX . 'SKU'          => array(static::COLUMN_MULTIPLE => true),
                static::VARIANT_PREFIX . 'Price'        => array(static::COLUMN_MULTIPLE => true),
                static::VARIANT_PREFIX . 'Quantity'     => array(static::COLUMN_MULTIPLE => true),
                static::VARIANT_PREFIX . 'Weight'       => array(static::COLUMN_MULTIPLE => true),
                static::VARIANT_PREFIX . 'Image'        => array(static::COLUMN_MULTIPLE => true),
                static::VARIANT_PREFIX . 'ImageAlt'     => array(static::COLUMN_MULTIPLE => true),
            );
        }

        return $columns;
    }

    /**
     * Get attribute column data
     *
     * @param \XLite\Model\Attribute $attribute Attribute object
     *
     * @return array
     */
    protected function getAttributeColumn($attribute)
    {
        $column = parent::getAttributeColumn($attribute);

        $column[static::COLUMN_MULTIPLE] = true;

        return $column;
    }

    // }}}

    // {{{ Getters and formatters

    /**
     * Get column value for 'variantSKU' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getVariantSKUColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['variant'])
            ? ''
            : $this->getColumnValueByName($dataset['variant'], 'sku');
    }

    /**
     * Get column value for 'variantPrice' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getVariantPriceColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['variant']) || $this->getColumnValueByName($dataset['variant'], 'defaultPrice')
            ? ''
            : $this->getColumnValueByName($dataset['variant'], 'price');
    }

    /**
     * Get column value for 'variantQuantity' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getVariantQuantityColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['variant']) || $this->getColumnValueByName($dataset['variant'], 'defaultAmount')
            ? ''
            : $this->getColumnValueByName($dataset['variant'], 'amount');
    }

    /**
     * Get column value for 'variantWeight' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getVariantWeightColumnValue(array $dataset, $name, $i)
    {
        return empty($dataset['variant']) || $this->getColumnValueByName($dataset['variant'], 'defaultWeight')
            ? ''
            : $this->getColumnValueByName($dataset['variant'], 'weight');
    }

    /**
     * Get column value for abstract 'attribute' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getAttributeColumnValue(array $dataset, $name, $i)
    {
        $columns = $this->getColumns();
        $column = $columns[$name];

        $attribute = !empty($dataset['attribute']) ? $dataset['attribute'] : null;

        if (null === $attribute && $column['attributeIsProduct']) {
            $attribute = $this->findProductSpecifiedVariantAttribute($dataset['model'], $column['attributeName']);
        }

        if (null === $attribute && $column['attributeIsClass']) {
            $attribute = $this->findProductClassVariantAttribute(
                $dataset['model'],
                $column['attributeName'],
                $column['attributeGroup']
            );
        }

        if (null === $attribute) {
            $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($column['attributeId']);
        }

        return !empty($dataset['variant']) && $attribute->isVariable($dataset['model'])
            ? $dataset['variant']->getAttributeValue($attribute)->asString()
            : ($i ? '' : parent::getAttributeColumnValue($dataset, $name, $i));
    }

    /**
     * Get column value for 'variantImage' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getVariantImageColumnValue(array $dataset, $name, $i)
    {
        $result = array();
        if (!empty($dataset['variant']) && null !== $dataset['variant']->getImage() ) {
            $result[] = $this->formatImageModel($dataset['variant']->getImage());
        }

        return $result;
    }

    /**
     * Get column value for 'variantImageAlt' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return array
     */
    protected function getVariantImageAltColumnValue(array $dataset, $name, $i)
    {
        $result = array();
        if (!empty($dataset['variant']) && null !== $dataset['variant']->getImage() ) {
            $result[] = $dataset['variant']->getImage()->getAlt();
        }       

        return $result;
    }

    /**
     * Returns product specified variant attribute by name
     *
     * @param \XLite\Model\Product $model Product
     * @param string               $name  Attribute name
     *
     * @return \XLite\Model\Attribute
     */
    protected function findProductSpecifiedVariantAttribute($model, $name)
    {
        /** @var \XLite\Model\Attribute $item */
        return array_reduce($model->getVariantsAttributes()->toArray(), function ($carry, $item) use ($name) {
            return null === $carry && $name === $item->getName() && (bool) $item->getProduct()
                ? $item
                : $carry;
        }, null);
    }

    /**
     * Returns product class variant attribute by name and group
     *
     * @param \XLite\Model\Product $model Product
     * @param string               $name  Attribute name
     * @param string               $group Attribute group
     *
     * @return \XLite\Model\Attribute
     */
    protected function findProductClassVariantAttribute($model, $name, $group)
    {
        /** @var \XLite\Model\Attribute $item */
        return array_reduce($model->getVariantsAttributes()->toArray(), function ($carry, $item) use ($name, $group) {
            return null === $carry
            && $name === $item->getName()
            && $group === ($item->getAttributeGroup() ? $item->getAttributeGroup()->getName() : '')
            && (bool) $item->getProductClass()
                ? $item
                : $carry;
        }, null);
    }

    // }}}
}
