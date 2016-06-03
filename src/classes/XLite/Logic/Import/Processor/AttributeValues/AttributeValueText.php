<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Processor\AttributeValues;

/**
 * Product attributes values import processor
 */
class AttributeValueText extends \XLite\Logic\Import\Processor\AttributeValues\AAttributeValue
{
    /**
     * Attribute type
     *
     * @var string
     */
    protected $attributeType = 'T';


    /**
     * Get title
     *
     * @return string
     */
    static public function getTitle()
    {
        return static::t('Product attributes values (Textarea) has been imported');
    }

    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns['value'][static::COLUMN_IS_TAGS_ALLOWED] = true;

        return $columns;
    }


    /**
     * Create model
     *
     * @param array $data Data
     *
     * @return \XLite\Model\AttributeValue\AAttributeValue
     */
    protected function createModel(array $data)
    {
        $data['owner'] = $this->normalizeValueAsBoolean($data['owner']);

        $product = $this->getProduct($data['productSKU']);

        $attribute = $this->getAttribute($data);

        if (!$attribute) {
            $attribute = $this->createAttribute($data);
        }

        $attribute->setAttributeValue($product, $data);

        return null;
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AttributeValue\AttributeValueText');
    }

    /**
     * Import 'priceModifier' value
     *
     * @param \XLite\Model\AttributeValue\AAttributeValue $model Attribute value object
     * @param mixed                                       $value  Value
     * @param array                                       $column Column info
     *
     * @return void
     */
    protected function importPriceModifierColumn($model, $value, array $column)
    {
    }

    /**
     * Import 'weightModifier' value
     *
     * @param \XLite\Model\AttributeValue\AAttributeValue $model Attribute value object
     * @param mixed                                       $value  Value
     * @param array                                       $column Column info
     *
     * @return void
     */
    protected function importWeightModifierColumn($model, $value, array $column)
    {
    }
}
