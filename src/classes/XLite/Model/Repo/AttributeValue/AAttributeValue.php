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
abstract class AAttributeValue extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_PRODUCT   = 'product';
    const SEARCH_ATTRIBUTE = 'attribute';
    const SEARCH_VALUE     = 'value';

    /**
     * Postprocess common
     *
     * @param array $data Data
     *
     * @return array
     */
    abstract protected function postprocessCommon(array $data);

    // {{{ Search

    /**
     * Find multiple attributes
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    public function findMultipleAttributes(\XLite\Model\Product $product)
    {
        $data = $this->createQueryBuilder('av')
            ->select('a.id')
            ->addSelect('COUNT(a.id) cnt')
            ->innerJoin('av.attribute', 'a')
            ->andWhere('av.product = :product')
            ->andWhere('a.productClass is null OR a.productClass = :productClass')
            ->having('COUNT(a.id) > 1')
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

        return \XLite\Core\Database::getRepo('XLite\Model\Attribute')->findMultipleAttributes($product, $ids);
    }

    /**
     * Find multiple attributes
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    public function findOneByImportConditions(array $conditions)
    {
        $result = null;

        // Search for product
        $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBy(array('sku' => $conditions['productSKU']));

        if ($product) {

            // Search for attribute

            $cnd = new \XLite\Core\CommonCell();

            if ($conditions['owner']) {
                // Custom product attribute
                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = $product;

            } else {

                if (!empty($conditions['class'])) {

                    // Class attribute

                    $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT} = null;

                    $class = \XLite\Core\Database::getRepo('XLite\Model\ProductClass')->findOneByName($conditions['class']);

                    if ($class) {
                        $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS} = $class;

                        if (!empty($conditions['group'])) {
                            $group = \XLite\Core\Database::getRepo('XLite\Model\AttributeGroup')->findOneByName($conditions['group']);

                            if ($group) {
                                $cnd->{\XLite\Model\Repo\Attribute::SEARCH_ATTRIBUTE_GROUP} = $group;
                            }
                        }
                    }

                } else {
                    // Global attribute
                    $cnd->{\XLite\Model\Repo\Attribute::SEARCH_PRODUCT_CLASS} = null;
                }
            }

            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_TYPE} = $conditions['type'];
            $cnd->{\XLite\Model\Repo\Attribute::SEARCH_NAME} = $conditions['name'];

            $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->search($cnd);

            if ($attribute) {
                $attribute = reset($attribute);

                // Search for attribute value
                if (!isset($conditions['value'])) {
                    $conditions['value'] = '';
                }

                $result = $this->findOneByValue($product, $attribute, $conditions['value']);
            }
        }

        return $result;
    }

    /**
     * Find one by value
     *
     * @param \XLite\Model\Product   $product   Product object
     * @param \XLite\Model\Attribute $attribute Attribute object
     * @param mixed                  $value     Value
     *
     * @return \XLite\Model\AttributeValue\AAtributeValue
     */
    protected function findOneByValue($product, $attribute, $value)
    {
        return $this->defineFindOneByValueQuery($product, $attribute, $value)->getSingleResult();
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
        return $this->createQueryBuilder('av')
            ->andWhere('av.attribute = :attribute')
            ->andWhere('av.product = :product')
            ->setParameter('attribute', $attribute)
            ->setParameter('product', $product);
    }

    /**
     * Find common
     *
     * @param \XLite\Model\Product $product Product
     *
     * @return array
     */
    public function findCommonValues(\XLite\Model\Product $product)
    {
        return $this->postprocessCommon(
            $this->createQueryBuilderCommonValues($product)->getArrayResult()
        );
    }

    /**
     * Find attribute value which will be considered as a default if attribute has not specific default value
     *
     * @param array $data Data to search: array('product' => ..., 'attribute' => ...)
     *
     * @return \XLite\Model\AttributeValue\AAttributeValue
     */
    public function findDefaultAttributeValue($data)
    {
        return $this->findOneBy($data);
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
        return $this->createQueryBuilder('av')
            ->addSelect('a.id attrId')
            ->innerJoin('av.attribute', 'a')
            ->andWhere('av.product = :product')
            ->andWhere('a.product is null')
            ->setParameter('product', $product);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndProduct(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.product = :product')
                ->setParameter('product', $value);
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     */
    protected function prepareCndAttribute(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.attribute = :attribute')
                ->setParameter('attribute', $value);
        }
    }

    // }}}

    // {{{ Export routines

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForExportQuery()
    {
        $qb = $this->createPureQueryBuilder();

        return $qb->select(
            'COUNT(DISTINCT ' . $qb->getMainAlias() . '.' . $this->getPrimaryKeyField() . ')'
        );
    }

    /**
     * Define export iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineExportIteratorQueryBuilder($position)
    {
        return $this->createPureQueryBuilder()
            ->setFirstResult($position)
            ->setMaxResults(1000000000);
    }

    // }}}
}
