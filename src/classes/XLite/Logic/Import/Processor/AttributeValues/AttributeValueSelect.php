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
class AttributeValueSelect extends \XLite\Logic\Import\Processor\AttributeValues\AAttributeValue
{
    /**
     * Attribute type
     *
     * @var string
     */
    protected $attributeType = 'S';


    /**
     * Get title
     *
     * @return string
     */
    static public function getTitle()
    {
        return static::t('Product attributes values (Plain text) has been imported');
    }

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\AttributeValue\AttributeValueSelect');
    }

    /**
     * Get attribute value data
     *
     * @param array                  $data      Import row data
     * @param \XLite\Model\Attribute $attribute Attribute object
     *
     * @return array
     */
    protected function getAttributeValueData($data, $attribute)
    {
        $option = \XLite\Core\Database::getRepo('XLite\Model\AttributeOption')->findOneByNameAndAttribute($data['value'], $attribute);

        if (!$option) {
            $option = new \XLite\Model\AttributeOption();
            $option->setAttribute($attribute);
            $option->setName($data['value']);
            \XLite\Core\Database::getEM()->persist($option);
        }

        return array(
            'attribute_option' => $option,
        );
    }

    /**
     * Import 'value' value
     *
     * @param \XLite\Model\AttributeValue\AAttributeValue $model Attribute value object
     * @param mixed                                       $value  Value
     * @param array                                       $column Column info
     *
     * @return void
     */
    protected function importValueColumn($model, $value, array $column)
    {
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
        $model->setModifier($value, 'price');
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
        $model->setModifier($value, 'weight');
    }
}
