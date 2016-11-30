<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\XC\ProductFilter\Model\QueryBuilder;

/**
 * Product query builder
 *
 * @Decorator\Depend("XC\ProductFilter")
 */
class Product extends \XLite\Model\QueryBuilder\Product implements \XLite\Base\IDecorator
{
    const VARIANT_ATTRIBUTES_ALIAS = 'variant_attributes';
    const VARIANTS_ALIAS = 'variants';
    const ATTRIBUTE_VALUE_S_ALIAS = 'attribute_values_s';
    const ATTRIBUTE_VALUE_C_ALIAS = 'attribute_values_c';

    private $joinsAssigned = false;

    /**
     * Assign attribute condition
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param mixed                  $value     Value
     */
    public function assignAttributeCondition(\XLite\Model\Attribute $attribute, $value)
    {
        if (!$this->joinsAssigned) {
            $this->joinsAssigned = true;
            $this->leftJoin('p.variantsAttributes', static::VARIANT_ATTRIBUTES_ALIAS);
            $this->leftJoin('p.variants', static::VARIANTS_ALIAS);
            $this->leftJoin(static::VARIANTS_ALIAS . '.attributeValueS', static::ATTRIBUTE_VALUE_S_ALIAS);
            $this->leftJoin(static::VARIANTS_ALIAS . '.attributeValueC', static::ATTRIBUTE_VALUE_C_ALIAS);
        }

        parent::assignAttributeCondition($attribute, $value);
    }

    /**
     * Return condition for select
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param mixed                  $value     Condition data
     * @param string                 $alias     Alias
     *
     * @return string
     */
    protected function getConditionSelect(\XLite\Model\Attribute $attribute, $value, $alias)
    {
        $where = parent::getConditionSelect($attribute, $value, $alias);
        if (
            $value
            && is_array($value)
        ) {
            foreach ($value as $k => $v) {
                if (!is_numeric($v)) {
                    unset($value[$k]);
                }
            }
            if ($value) {
                $attr = 'attribute' . $attribute->getId();
                $where .= sprintf(' AND ((:%s = %s.id AND %s.id = %s.id) OR %s.id IS NULL)',
                    $attr,
                    static::VARIANT_ATTRIBUTES_ALIAS,
                    $alias,
                    static::ATTRIBUTE_VALUE_S_ALIAS,
                    static::VARIANT_ATTRIBUTES_ALIAS);
            }
        }

        return $where;
    }

    /**
     * Return condition for checkbox
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param mixed                  $value     Condition data
     * @param string                 $alias     Alias
     *
     * @return string
     */
    protected function getConditionCheckbox(\XLite\Model\Attribute $attribute, $value, $alias)
    {
        $where = parent::getConditionCheckbox($attribute, $value, $alias);

        if (!empty($where)) {
            $attr = 'attribute' . $attribute->getId();
            $where .= sprintf(' AND ((:%s = %s.id AND %s.id = %s.id) OR %s.id IS NULL)',
                $attr,
                static::VARIANT_ATTRIBUTES_ALIAS,
                $alias,
                static::ATTRIBUTE_VALUE_C_ALIAS,
                static::VARIANT_ATTRIBUTES_ALIAS);
        }

        return $where;
    }
}