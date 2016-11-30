<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Module\XC\ProductFilter\Model;

/**
 * Category
 *
 * @Decorator\Depend("XC\ProductFilter")
 */
class Category extends \XLite\Model\Category implements \XLite\Base\IDecorator
{
    const VARIANT_ATTRIBUTES_ALIAS = 'variant_attributes';
    const VARIANTS_ALIAS = 'variants';
    const ATTRIBUTE_VALUE_S_ALIAS = 'attribute_values_s';

    /**
     * Return available category attribute values query builder
     *
     * @param \XLite\Model\Attribute $attribute
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function getAvailableAttributeValueSelectOptionsQueryBuilder(\XLite\Model\Attribute $attribute)
    {
        $qb = parent::getAvailableAttributeValueSelectOptionsQueryBuilder($attribute);

        $qb->leftJoin('product.variantsAttributes', static::VARIANT_ATTRIBUTES_ALIAS);
        $qb->leftJoin('product.variants', static::VARIANTS_ALIAS);
        $qb->leftJoin(static::VARIANTS_ALIAS . '.attributeValueS', static::ATTRIBUTE_VALUE_S_ALIAS);

        $where = sprintf('(:attribute = %s.id AND %s.id = %s.id) OR %s.id IS NULL',
            static::VARIANT_ATTRIBUTES_ALIAS,
            'av',
            static::ATTRIBUTE_VALUE_S_ALIAS,
            static::VARIANT_ATTRIBUTES_ALIAS);

        $qb->andWhere($where);

        return $qb;
    }
}