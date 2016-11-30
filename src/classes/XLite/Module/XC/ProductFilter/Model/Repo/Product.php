<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Model\Repo;

/**
 * The "product" model repository
 *
 */
abstract class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    /**
     * Allowable search params
     */
    const P_ATTRIBUTE = 'attribute';
    const P_FILTER    = 'filter';
    const P_IN_STOCK  = 'inStock';
    const P_QUICK_DATA_MEMBERSHIP = 'QuickDataMembership';

    /**
     * Allowable search modes
     */
    const SEARCH_MODE_SCALAR = 'searchModeScalar';

    /**
     * Scalar mode custom seach conditions
     */
    const P_SCALAR_FUNCTION = 'scalarFunction';
    const P_SCALAR_PROPERTY = 'scalarProperty';
    const P_SCALAR_SELECT = 'scalarSelect';

    /**
     * Get default property for scalar search
     */
    protected function getDefaultScalarModeProperty()
    {
        return $this->getPrimaryKeyField();
    }

    /**
     * Get default scalar mode function
     */
    protected function getDefaultScalarModeFunction()
    {
        return 'COUNT';
    }

    /**
     * Get search modes handlers
     *
     * @return array
     */
    protected function getSearchModes()
    {
        return array_merge(
            parent::getSearchModes(),
            array(
                static::SEARCH_MODE_SCALAR     => 'searchScalar',
            )
        );
    }

    /**
     * Excluded search conditions
     *
     * @return array
     */
    protected function getExcludedConditions()
    {
        return array_merge(
            parent::getExcludedConditions(),
            array(
                static::P_SCALAR_FUNCTION => static::EXCLUDE_FROM_ANY,
                static::P_SCALAR_PROPERTY => static::EXCLUDE_FROM_ANY,
                static::P_SCALAR_SELECT => static::EXCLUDE_FROM_ANY,
            )
        );
    }

    /**
     * Search scalar routine.
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    protected function searchScalar()
    {
        $queryBuilder = $this->searchState['queryBuilder'];
        $cnd = $this->searchState['currentSearchCnd'];

        if ($cnd->{static::P_SCALAR_SELECT}) {
            $select = $cnd->{static::P_SCALAR_SELECT};
        } else {
            $property = $cnd->{static::P_SCALAR_PROPERTY} ?: $this->getDefaultScalarModeProperty();

            $key = $this->getMainAlias($queryBuilder) . '.' . $property;
            $function = $cnd->{static::P_SCALAR_FUNCTION} ?: $this->getDefaultScalarModeFunction();

            $select = sprintf('%s(DISTINCT %s)', $function, $key);
        }

        $queryBuilder = $queryBuilder
            ->select($select)
            ->resetDQLPart('groupBy')
            ->resetDQLPart('orderBy');

        return $queryBuilder->getSingleScalarResult();
    }

    /**
     * Find filtered product classes
     *
     * @param \XLite\Core\CommonCell $cnd Search condition
     *
     * @return array
     */
    public function findFilteredProductClasses(\XLite\Core\CommonCell $cnd)
    {
        $result = array();

        $this->searchState['currentSearchCnd']  = $cnd;
        $this->searchState['searchMode']        = static::SEARCH_MODE_ENTITIES;
        $this->searchState['queryBuilder']      = $this->processQueryBuilder();

        $data = $this->searchState['queryBuilder']
            ->innerJoin('p.productClass', 'class')
            ->innerJoin('class.attributes', 'attr')
            ->andWhere('attr.type IN (\'' . implode('\',\'', \XLite\Model\Attribute::getFilteredTypes()) . '\')')
            ->andWhere('p.productClass is not null')
            ->GroupBy('p.productClass')
            ->getOnlyEntities();

        foreach ($data as $product) {
            $result[] = $product->getProductClass();
        }

        return $result;
    }

    /**
     * Get dissalowed for filter keys
     *
     * @return array
     */
    protected function getDisallowedKeys()
    {
        return array(
            static::P_ORDER_BY
        );
    }

    /**
     * Check if filter param is valid and allowed to be in filter
     *
     * @param  string  $key   Key/name
     * @param  string  $value Value
     * @return boolean
     */
    protected function isValidFiltedParam($key, $value)
    {
        $result = true;

        if (in_array($key, $this->getDisallowedKeys())) {
            $result = false;
        }

        return $result;
    }

    /**
     * Prepare filter search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndFilter(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (is_array($value) && $value) {
            foreach ($value as $key => $val) {
                if ($this->isValidFiltedParam($key, $value)) {
                    $this->callSearchConditionHandler($val, $key);
                }
            }
        }
    }

    /**
     * Prepare attribute search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param array                                   $value        Condition data
     *
     * @return void
     */
    protected function prepareCndAttribute(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        if (is_array($value) && $value) {
            $attributes = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->findByIds(array_keys($value));
            if ($attributes) {
                foreach ($attributes as $attribute) {
                    if (isset($value[$attribute->getId()])) {
                        $queryBuilder->assignAttributeCondition($attribute, $value[$attribute->getId()]);
                    }
                }
            }
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndInStock(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $this->prepareCndInventory($queryBuilder, static::INV_IN);
            $this->prepareCndArrivalDate($queryBuilder, array(\XLite\Core\Converter::getDayEnd(\XLite\Base\SuperClass::getUserTime())));
        }
    }

    /**
     * Prepare quick data membership search condition
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder to prepare
     * @param array                                   $value        Condition data
     *
     * @return void
     */
    protected function prepareCndQuickDataMembership(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->linkLeft($this->getMainAlias($queryBuilder) . '.quickData', 'qdm');

        if ($value) {
            $queryBuilder->andWhere('qdm.membership = :membership')
                ->setParameter('membership', $value);
        } else {
            $queryBuilder->andWhere('qdm.membership IS NULL');
        }
    }
}
