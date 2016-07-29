<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\Repo\AttributeValue;

/**
 * Attribute values repository
 */
abstract class AAttributeValue extends \XLite\Model\Repo\AttributeValue\AAttributeValue implements \XLite\Base\IDecorator
{
    /**
     * Find all attributes
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    public function findAllAttributes(\XLite\Model\Product $product)
    {
        $data = $this->createQueryBuilder('av')
            ->select('a.id')
            ->innerJoin('av.attribute', 'a')
            ->andWhere('av.product = :product')
            ->andWhere('a.productClass is null OR a.productClass = :productClass')
            ->setParameter('product', $product)
            ->setParameter('productClass', $product->getProductClass())
            ->addGroupBy('a.id')
            ->addOrderBy('a.position', 'ASC')
            ->getResult();

        $ids = array();
        if ($data) {
            foreach ($data as $v) {
                $ids[] = $v['id'];
            }
        }

        return $ids
            ? \XLite\Core\Database::getRepo('XLite\Model\Attribute')->findByIds($ids)
            : array();
    }
}
