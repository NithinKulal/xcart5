<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Logic;

/**
 * Attribute surcharges
 */
class AttributeSurcharge extends \XLite\Logic\AttributeSurcharge implements \XLite\Base\IDecorator
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
        return parent::isApply($model, $property, $behaviors, $purpose)
            || $model instanceOf \XLite\Module\XC\ProductVariants\Model\ProductVariant;
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
        return $model instanceOf \XLite\Module\XC\ProductVariants\Model\ProductVariant
            ? $model->getProduct()->getAttrValues()
            : parent::getAttributeValues($model);
    }
}
