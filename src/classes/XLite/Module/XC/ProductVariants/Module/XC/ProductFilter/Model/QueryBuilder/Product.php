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

    /**
     * Assign attribute condition
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param mixed                  $value     Value
     */
    public function assignAttributeCondition(\XLite\Model\Attribute $attribute, $value)
    {
        parent::assignAttributeCondition($attribute, $value);

        $alias = 'variantsAttributes' . $attribute->getId();
        $this->leftJoin('p.variantsAttributes', $alias, 'WITH', $alias . ' = ' . $attribute->getId());

        $this->linkLeft('p.variants');
        $valueAlias = 'variantAttributeValues' . $attribute->getId();
        $attributeAlias = 'av' . $attribute->getId();
        $this->leftJoin(
            'variants.attributeValue' . $attribute->getType(),
            $valueAlias,
            'WITH',
            $valueAlias . ' = ' . $attributeAlias
        );
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

        if (!empty($where)) {
            $alias      = 'variantsAttributes' . $attribute->getId();
            $valueAlias = 'variantAttributeValues' . $attribute->getId();
            $where .= sprintf(
                ' AND ((%s.id IS NOT NULL AND %s.id IS NOT NULL) OR %s.id IS NULL)',
                $alias,
                $valueAlias,
                $alias
            );
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
            $alias      = 'variantsAttributes' . $attribute->getId();
            $valueAlias = 'variantAttributeValues' . $attribute->getId();
            $where .= sprintf(
                ' AND ((%s.id IS NOT NULL AND %s.id IS NOT NULL) OR %s.id IS NULL)',
                $alias,
                $valueAlias,
                $alias
            );
        }

        return $where;
    }
}