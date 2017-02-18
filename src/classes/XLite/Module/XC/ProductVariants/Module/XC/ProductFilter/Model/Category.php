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

        $qb->leftJoin('product.variantsAttributes', 'variantsAttributes', 'WITH', 'variantsAttributes = av.attribute');
        $qb->leftJoin('product.variants', 'variants');
        $qb->leftJoin('variants.attributeValueS', 'attributeValuesS', 'WITH', 'attributeValuesS.attribute = av');

        $qb->andWhere('(variantsAttributes.id IS NULL OR attributeValuesS.id IS NOT NULL)');
        return $qb;
    }
}