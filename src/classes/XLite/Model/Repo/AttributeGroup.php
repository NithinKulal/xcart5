<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Attribute groups repository
 */
class AttributeGroup extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */
    const SEARCH_PRODUCT_CLASS = 'productClass';
    const SEARCH_NAME          = 'name';

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    // {{{ Search

    /**
     * Search by product class
     *
     * @param type $productClass
     *
     * @return array
     */
    public function findByProductClass($productClass)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->productClass = $productClass;
        return $this->search($cnd);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareCndProductClass(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        if ($value) {
            $queryBuilder->andWhere('a.productClass = :productClass')
                ->setParameter('productClass', $value);

        } else {
            $queryBuilder->andWhere('a.productClass is null');
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param mixed                      $value        Condition OPTIONAL
     *
     * @return void
     * @see    ____func_see____
     * @since  1.0.0
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value = null)
    {
        $queryBuilder->andWhere('translations.name = :name')
            ->setParameter('name', $value);
    }

    // }}}

    // {{{ Find one by name

    /**
     * Find entity by name (any language)
     *
     * @param string  $name      Name
     * @param boolean $countOnly Count only OPTIONAL
     *
     * @return \XLite\Model\AttributeGroup|integer
     */
    public function findOneByName($name, $countOnly = false)
    {
        return $countOnly
            ? count($this->defineOneByNameQuery($name)->getResult())
            : $this->defineOneByNameQuery($name)->getSingleResult();
    }

    /**
     * Define query builder for findOneByName() method
     *
     * @param string $name Name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByNameQuery($name)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name);

        return $qb;
    }

    // }}}
}
