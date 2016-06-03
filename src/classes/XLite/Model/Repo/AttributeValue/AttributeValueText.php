<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo\AttributeValue;

/**
 * Attribute values repository
 */
class AttributeValueText extends \XLite\Model\Repo\AttributeValue\AAttributeValue
{
    /**
     * Find editable attributes of product
     *
     * @param \XLite\Model\Product $product Product object
     *
     * @return array
     */
    public function findEditableAttributes(\XLite\Model\Product $product)
    {
        $data = $this->createQueryBuilder('av')
            ->select('a.id')
            ->addSelect('COUNT(a.id) cnt')
            ->innerJoin('av.attribute', 'a')
            ->andWhere('av.product = :product AND av.editable = :true')
            ->andWhere('a.productClass is null OR a.productClass = :productClass')
            ->setParameter('product', $product)
            ->setParameter('productClass', $product->getProductClass())
            ->setParameter('true', true)
            ->addGroupBy('a.id')
            ->addOrderBy('a.position', 'ASC')
            ->getResult();

        $ids = array();
        if ($data) {
            foreach ($data as $v) {
                $ids[] = $v['id'];
            }
        }

        return \XLite\Core\Database::getRepo('XLite\Model\Attribute')->findMultipleAttributes($product, $ids);
    }

    /**
     * Return QueryBuilder for common values
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createQueryBuilderCommonValues(\XLite\Model\Product $product)
    {
        return parent::createQueryBuilderCommonValues($product)
            ->addSelect('translations');
    }

    /**
     * Postprocess common
     *
     * @param array $data Data
     *
     * @return array
     */
    protected function postprocessCommon(array $data)
    {
        $result = array();

        foreach ($data as $v) {
            $result[$v['attrId']] = array(
                'value'    => $v[0]['translations'][0]['value'],
                'editable' => $v[0]['editable'],
            );
        }

        return $result;
    }

    /**
     * Define QueryBuilder for findOneByValue() method
     *
     * @param \XLite\Model\Product   $product   Product object
     * @param \XLite\Model\Attribute $attribute Attribute object
     * @param mixed                  $value     Value
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFindOneByValueQuery($product, $attribute, $value)
    {
        $qb = parent::defineFindOneByValueQuery($product, $attribute, $value);

        $qb->andWhere('translations.value = :value')
            ->setParameter('value', $value);

        return $qb;
    }
}
