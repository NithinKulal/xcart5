<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Model\Repo;

/**
 * The "product" repo class
 */
abstract class Product extends \XLite\Model\Repo\Product implements \XLite\Base\IDecorator
{
    protected $csProductIds = null;


    /**
     * Common search
     *
     * @param \XLite\Core\CommonCell $cnd           Search conditions                   OPTIONAL
     * @param boolean|string         $searchMode    Return items list or only its size  OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function search(\XLite\Core\CommonCell $cnd = null, $searchMode = self::SEARCH_MODE_ENTITIES)
    {
        if (
            $cnd->{static::P_SUBSTRING}
            && \XLite\Module\QSL\CloudSearch\Main::doSearch()
            && !\XLite::isAdminZone()
        ) {
            // We initialize IDS for CloudSearch functionality
            $this->getCSProductIds($cnd->{static::P_SUBSTRING});
        }

        return parent::search($cnd, $searchMode);
    }

    protected function getCSProductIds($value = null)
    {
        if (is_null($this->csProductIds)) {
            $this->csProductIds = \XLite\Module\QSL\CloudSearch\Core\ServiceApiClient::getInstance()->search($value);
        }
        
        return $this->csProductIds;
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (
            $value
            && !$this->isCountSearchMode()
        ) {
            list($sort, ) = $this->getSortOrderValue($value);

            $needParent = true;

            if ('relevance' == $sort) {
                // We add the IDs additional condition
                // We get last IDS from the previous query by string
                $ids = $this->getCSProductIds();

                if (!empty($ids)) {
                    $idsInCondition = $queryBuilder->getInCondition($ids, 'arr');

                    $queryBuilder->resetDQLPart('orderBy');
                    $queryBuilder
                        ->addSelect('field(p.product_id, ' . $idsInCondition . ') as field_product_id')
                        ->addOrderBy('field_product_id', 'asc');

                    $needParent = false;
                }
            }

            if ($needParent) {
                parent::prepareCndOrderBy($queryBuilder, $value);
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
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (
            $value
            && \XLite\Module\QSL\CloudSearch\Main::doSearch()
            && !\XLite::isAdminZone()
        ) {
            // We add the IDs additional condition
            $ids = $this->getCSProductIds($value);

            if (!empty($ids)) {
                $idsInCondition = $queryBuilder->getInCondition($ids, 'arr');
                $queryBuilder->andWhere('p.product_id IN (' . $idsInCondition . ')');
            } else {
                // No result in Cloud Search
                parent::prepareCndSubstring($queryBuilder, $value);
            }
        } else {
            parent::prepareCndSubstring($queryBuilder, $value);
        }
    }
}
