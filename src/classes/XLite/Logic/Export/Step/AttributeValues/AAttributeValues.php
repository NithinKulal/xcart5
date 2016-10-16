<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export\Step\AttributeValues;

/**
 * Products attribute values abstract class
 */
abstract class AAttributeValues extends \XLite\Logic\Export\Step\Base\I18n
{
    // {{{ Data

    /**
     * Get filename
     *
     * @return string
     */
    protected function getFilename()
    {
        return 'product-attributes.csv';
    }

    // }}}

    // {{{ Columns

    /**
     * Build header
     *
     * @return void
     */
    protected function buildHeader()
    {
        if (!$this->generator->getOptions()->isAttrHeaderBuilt) {
            parent::buildHeader();
            $this->generator->getOptions()->isAttrHeaderBuilt = true;
        }
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = array(
            'productSKU'     => array(),
            'type'           => array(),
            'name'           => array(),
            'class'          => array(static::COLUMN_GETTER => 'getClassColumnValue'),
            'group'          => array(static::COLUMN_GETTER => 'getGroupColumnValue'),
            'owner'          => array(),
            'value'          => array(),
            'default'        => array(),
            'priceModifier'  => array(),
            'weightModifier' => array(),
            'editable'       => array(), // Specific field, used only for textarea attribute type
        );

        return $columns;
    }

    // }}}

    // {{{ Getters and formatters

    /**
     * Get column value for 'productSKU' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getProductSKUColumnValue(array $dataset, $name, $i)
    {
        $product = $dataset['model']->getProduct();

        return $product ? $product->getSku() : '';
    }

    /**
     * Get column value for 'name' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getNameColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getAttribute()->getName();
    }

    /**
     * Get column value for 'sku' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getValueColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->asString();
    }

    /**
     * Get column value for 'type' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getTypeColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getAttribute()->getType();
    }

    /**
     * Get column value for 'class' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getClassColumnValue(array $dataset, $name, $i)
    {
        $class = $dataset['model']->getAttribute()->getProductClass();

        return $class
            ? $class->getName()
            : '';
    }

    /**
     * Get column value for 'group' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getGroupColumnValue(array $dataset, $name, $i)
    {
        $group = $dataset['model']->getAttribute()->getAttributeGroup();

        return $group
            ? $group->getName()
            : '';
    }

    /**
     * Get column value for 'priceModifier' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getPriceModifierColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getModifier('price');
    }

    /**
     * Get column value for 'weightModifier' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getWeightModifierColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getModifier('weight');
    }

    /**
     * Get column value for 'weightModifier' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getDefaultColumnValue(array $dataset, $name, $i)
    {
        $product = $dataset['model']->getProduct();

        return $product && $dataset['model']->getAttribute()->isMultiple($product) && method_exists($dataset['model'], 'isDefault')
            ? $dataset['model']->isDefault() ? 'Yes' : 'No'
            : 'No';
    }

    /**
     * Get column value for 'weightModifier' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getOwnerColumnValue(array $dataset, $name, $i)
    {
        return $dataset['model']->getAttribute()->getProduct() ? 'Yes' : 'No';
    }

    /**
     * Get column value for 'editable' column
     *
     * @param array   $dataset Dataset
     * @param string  $name    Column name
     * @param integer $i       Subcolumn index
     *
     * @return string
     */
    protected function getEditableColumnValue(array $dataset, $name, $i)
    {
        return (
            \XLite\Model\Attribute::TYPE_TEXT == $dataset['model']->getAttribute()->getType()
            && $dataset['model']->getEditable()
        )
        ? 'Yes'
        : '';
    }

    // }}}
}
