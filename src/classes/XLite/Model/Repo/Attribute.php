<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Attributes repository
 */
class Attribute extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_PRODUCT          = 'product';
    const SEARCH_PRODUCT_CLASS    = 'productClass';
    const SEARCH_ATTRIBUTE_GROUP  = 'attributeGroup';
    const SEARCH_TYPE             = 'type';
    const SEARCH_NAME             = 'name';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    /**
     * Find multiple attributes
     *
     * @param \XLite\Model\Product $product Product
     * @param array                $ids     Array of Ids
     *
     * @return array
     */
    public function findMultipleAttributes(\XLite\Model\Product $product, $ids)
    {
        return $ids
            ? $this->definefindMultipleAttributesQuery($product, $ids)->getResult()
            : array();
    }

    /**
     * Define query for findMultipleAttributes() method
     * 
     * @param \XLite\Model\Product $product Product
     * @param array                $ids     Attribute ID list
     *  
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function definefindMultipleAttributesQuery(\XLite\Model\Product $product, array $ids)
    {
        $qb = $this->createQueryBuilder('a');

        return $qb->leftJoin('a.attribute_properties', 'ap', 'WITH', 'ap.product = :product')
            ->addSelect('ap.position')
            ->andWhere('a.id IN (' . $qb->getInCondition($ids, 'arr') . ')')
            ->addGroupBy('a.id')
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
            $queryBuilder->andWhere('a.product = :attributeProduct')
                ->setParameter('attributeProduct', $value);

        } else {
            $queryBuilder->andWhere('a.product is null');
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
    protected function prepareCndProductClass(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if (is_null($value)) {
            $queryBuilder->andWhere('a.productClass is null');

        } elseif (is_object($value) && 'Doctrine\ORM\PersistentCollection' != get_class($value)) {
            $queryBuilder->andWhere('a.productClass = :productClass')
                ->setParameter('productClass', $value);

        } elseif ($value) {

            $ids = array();
            foreach ($value as $id) {
                if ($id) {
                    $ids[] = is_object($id) ? $id->getId() : $id;
                }
            }

            if ($ids) {
                $queryBuilder->linkInner('a.productClass')
                    ->andWhere($queryBuilder->expr()->in('productClass.id', $ids));
            }
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
    protected function prepareCndAttributeGroup(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.attributeGroup = :attributeGroup')
                ->setParameter('attributeGroup', $value);

        } else {
            $queryBuilder->andWhere('a.attributeGroup is null');
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
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            if (is_array($value)) {
                $queryBuilder->andWhere('a.type IN (\'' . implode("','", $value) . '\')');

            } else {
                $queryBuilder->andWhere('a.type = :type')
                    ->setParameter('type', $value);
            }
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
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            // Add additional join to translations with default language code
            $this->addDefaultTranslationJoins(
                $queryBuilder,
                $this->getMainAlias($queryBuilder),
                'defaults',
                \XLite::getDefaultLanguage()
            );

            $condition = $queryBuilder->expr()->orX();

            $condition->add('translations.name = :name');
            $condition->add('defaults.name = :name');
            if (\XLite\Core\Translation::DEFAULT_LANGUAGE !== \XLite::getDefaultLanguage()) {
                // Add additional join to translations with default-default ('en' at the moment) language code
                $this->addDefaultTranslationJoins(
                    $queryBuilder,
                    $this->getMainAlias($queryBuilder),
                    'defaultDefaults',
                    'en'
                );
                $condition->add('defaultDefaults.name = :name');
            }

            $queryBuilder->andWhere($condition)
                ->setParameter('name', $value);
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

    /**
     * Generate attribute values
     *
     * @param \XLite\Model\Product $product         Product
     * @param boolean              $useProductClass Use product class OPTIONAL
     *
     * @return void
     */
    public function generateAttributeValues(\XLite\Model\Product $product, $useProductClass = null)
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->productClass = $useProductClass ? $product->getProductClass() : null;
        $cnd->product = null;
        $cnd->type = array(
            \XLite\Model\Attribute::TYPE_CHECKBOX,
            \XLite\Model\Attribute::TYPE_SELECT,
            \XLite\Model\Attribute::TYPE_TEXT,
        );
        foreach ($this->search($cnd) as $a) {
            $a->addToNewProduct($product);
        }
    }

    /**
     * Get identifiers list for specified query builder object
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder
     * @param string                     $name  Name
     * @param mixed                      $value Value
     *
     * @return void
     */
    protected function addImportCondition(\Doctrine\ORM\QueryBuilder $qb, $name, $value)
    {
        if ('productClass' == $name && is_string($value)) {
            $alias = $qb->getMainAlias();
            $qb->linkInner($alias . '.productClass')
                ->linkInner('productClass.translations', 'productClassTranslations')
                ->andWhere('productClassTranslations.name = :productClass')
                ->setParameter('productClass', $value);

        } else {
            $result = parent::addImportCondition($qb, $name, $value);
        }
    }
}
