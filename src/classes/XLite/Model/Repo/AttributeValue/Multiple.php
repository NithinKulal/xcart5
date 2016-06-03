<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\AttributeValue;

/**
 * Multiple attribute values repository
 */
abstract class Multiple extends \XLite\Model\Repo\AttributeValue\AAttributeValue
{
    /**
     * Get modifier types by product 
     * 
     * @param \XLite\Model\Product $product Product
     *  
     * @return array
     */
    public function getModifierTypesByProduct(\XLite\Model\Product $product)
    {
        $price = $this->createQueryBuilder('av')
            ->select('a.id')
            ->innerJoin('av.attribute', 'a')
            ->andWhere('av.product = :product AND (a.productClass IS NULL OR a.productClass = :productClass) AND av.priceModifier != 0')
            ->setParameter('product', $product)
            ->setParameter('productClass', $product->getProductClass())
            ->addGroupBy('a.id')
            ->setMaxResults(1)
            ->getResult();

        $weight = $this->createQueryBuilder('av')
            ->select('a.id')
            ->innerJoin('av.attribute', 'a')
            ->andWhere('av.product = :product AND (a.productClass IS NULL OR a.productClass = :productClass) AND av.weightModifier != 0')
            ->setParameter('product', $product)
            ->setParameter('productClass', $product->getProductClass())
            ->addGroupBy('a.id')
            ->setMaxResults(1)
            ->getResult();

        if ($price || $weight) {
            $attrModifierPercent = $this->createQueryBuilder('av')
                ->select('a.id')
                ->innerJoin('av.attribute', 'a')
                ->andWhere('av.product = :product AND (a.productClass IS NULL OR a.productClass = :productClass) AND (av.weightModifier != 0 AND av.weightModifierType = :modifierType OR av.priceModifier != 0 AND av.priceModifierType = :modifierType)')
                ->setParameter('product', $product)
                ->setParameter('productClass', $product->getProductClass())
                ->setParameter('modifierType', \XLite\Model\AttributeValue\Multiple::TYPE_PERCENT)
                ->addGroupBy('a.id')
                ->setMaxResults(1)
                ->getResult();
        }

        return array(
            'price'  => !empty($price),
            'weight' => !empty($weight),
            'attrModifierPercent' => !empty($attrModifierPercent),
        );
    }

}
