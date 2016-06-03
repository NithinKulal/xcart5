<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic;

/**
 * Net price modificator: add attribute surcharge
 */
class AttributeSurcharge extends \XLite\Logic\ALogic
{
    /**
     * Check modificator - apply or not
     *
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return boolean
     */
    static public function isApply(\XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        return $model instanceOf \XLite\Model\OrderItem || $model instanceOf \XLite\Model\Product;
    }

    /**
     * Modify money
     *
     * @param float                $value     Value
     * @param \XLite\Model\AEntity $model     Model
     * @param string               $property  Model's property
     * @param array                $behaviors Behaviors
     * @param string               $purpose   Purpose
     *
     * @return void
     */
    static public function modifyMoney($value, \XLite\Model\AEntity $model, $property, array $behaviors, $purpose)
    {
        foreach (static::getAttributeValues($model) as $attributeValue) {
            if (
                $attributeValue instanceOf \XLite\Model\OrderItem\AttributeValue
                && $attributeValue->getAttributeValue()
            ) {
                $attributeValue = $attributeValue->getAttributeValue();
            }

            if (is_object($attributeValue)) {
                $value += $attributeValue->getAbsoluteValue('price');
            }
        }

        return $value;
    }

    /**
     * Return attribute values
     *
     * @param \XLite\Model\AEntity $model Model
     *
     * @return array
     */
    static protected function getAttributeValues(\XLite\Model\AEntity $model)
    {
        return $model instanceOf \XLite\Model\Product
            ? $model->getAttrValues()
            : $model->getAttributeValues();
    }
}
