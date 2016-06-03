<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\QSL\SpecialOffersBase\Model\Repo;

/**
 * Repository class for the Special Offer model.
 */
class SpecialOffer extends \XLite\Model\Repo\Base\I18n
{
    const SEARCH_ORDERBY          = 'orderBy';
    const SEARCH_LIMIT            = 'limit';
    const SEARCH_ENABLED          = 'enabled';
    const SEARCH_NAME             = 'name';
    const SEARCH_ACTIVE           = 'active';
    const SEARCH_VISIBLE_HOME     = 'visibleHome';
    const SEARCH_VISIBLE_OFFERS   = 'visibleOffers';

    /**
     *
     * Allowed sort criteria
     */
    const ORDER_BY_POSITION    = 'position';
    const ORDER_BY_NAME        = 'name';
    const ORDER_BY_ACTIVE_FROM = 'activeFrom';
    const ORDER_BY_ACTIVE_TILL = 'activeTill';

    /**
     * Cached search criteria.
     *
     * @var \XLite\Core\CommonCell
     */
    protected $currentSearchCnd = null;

    /**
     * Returns active offers.
     * 
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function findActiveOffers()
    {
        return $this->search($this->getActiveOffersConditions());
    }

    /**
     * Returns the default conditions to retrieve active offers.
     * 
     * @return \XLite\Core\CommonCell
     */
    public function getActiveOffersConditions()
    {
        $cnd = new \XLite\Core\CommonCell;
        $cnd->{static::SEARCH_ENABLED} = true;
        $cnd->{static::SEARCH_ACTIVE} = true;
        $cnd->{static::SEARCH_ORDERBY} = array(static::ORDER_BY_POSITION, 'ASC');
        
        return $cnd;
    }
    
    /**
     * Common search method.
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function search(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $queryBuilder = $this->getDefaultSearchQueryBuilder();
        $this->currentSearchCnd = $cnd;

        foreach ($this->currentSearchCnd as $key => $value) {
            $this->callSearchConditionHandler($value, $key, $queryBuilder, $countOnly);
        }

        return $countOnly
            ? $this->searchCount($queryBuilder)
            : $this->searchResult($queryBuilder);
    }

    /**
     * Search count only routine.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder routine
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchCount(\Doctrine\ORM\QueryBuilder $qb)
    {
        $qb->select('COUNT(DISTINCT o.offer_id)');

        return intval($qb->getSingleScalarResult());
    }

    /**
     * Search result routine.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder routine
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function searchResult(\Doctrine\ORM\QueryBuilder $qb)
    {
        return $qb->getResult();
    }

    /**
     * Get the default query builder used to retrieve brands.
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getDefaultSearchQueryBuilder()
    {
        return $this->createQueryBuilder('o')
            ->linkInner('o.offerType', 't')
            ->addSelect('t')
            ->linkInner('t.translations', 'tt')
            ->addSelect('tt');
    }
    
    /**
     * Call corresponded method to handle a search condition.
     *
     * @param mixed                      $value        Condition data
     * @param string                     $key          Condition name
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param boolean                    $countOnly    Count only flag
     *
     * @return void
     */
    protected function callSearchConditionHandler($value, $key, \Doctrine\ORM\QueryBuilder $queryBuilder, $countOnly)
    {
        if ($this->isSearchParamHasHandler($key)) {
            $this->{'prepareCnd' . ucfirst($key)}($queryBuilder, $value, $countOnly);
        }
    }

    /**
     * Check if param can be used for search.
     *
     * @param string $param Name of param to check
     *
     * @return boolean
     */
    protected function isSearchParamHasHandler($param)
    {
        return in_array($param, $this->getHandlingSearchParams());
    }

    /**
     * Return list of handling search params
     *
     * @return array
     */
    protected function getHandlingSearchParams()
    {
        return array(
            static::SEARCH_LIMIT,
            static::SEARCH_ORDERBY,
            static::SEARCH_ENABLED,
            static::SEARCH_NAME,
            static::SEARCH_ACTIVE,
            static::SEARCH_VISIBLE_HOME,
            static::SEARCH_VISIBLE_OFFERS,
        );
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndLimit(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        $queryBuilder->setFrameResults($value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value, $countOnly)
    {
        if (!$countOnly) {

            list($sort, $order) = $this->getSortOrderValue($value);
            if (!is_array($sort)) {
                $sort = array($sort);
                $order = array($order);
            }

            foreach ($sort as $key => $sortItem) {
                $field = $this->getOrderByField($sortItem);
                if ($field) {
                    $queryBuilder->addOrderBy($field, $order[$key]);
                }
            }
        }
    }

    /**
     * Prepare Enabled/Disabled search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 'o.enabled <> 0' : 'o.enabled = 0');
        }
    }

    /**
     * Prepare "Visisble/Hidden on the home page" search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndVisibleHome(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 'o.promoHome <> 0' : 'o.promoHome = 0');
        }
    }
    
    /**
     * Prepare "Visisble/Hidden on the Current Offers page" search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndVisibleOffers(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere($value ? 'o.promoOffers <> 0' : 'o.promoOffers = 0');
        }
    }

    /**
     * Prepare Is Active search condition.
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param integer                    $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndActive(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        // Skip special offers that has disabled offer types
        $queryBuilder->andWhere('t.enabled <> 0');
        
        if (is_numeric($value) || is_bool($value)) {
            $queryBuilder->andWhere(
                $value
                    ? '((o.activeFrom = 0) OR (o.activeFrom <= :current_time)) AND ((o.activeTill = 0) OR (o.activeTill >= :current_time))'
                    : '(o.activeFrom > :current_time) OR (o.activeTill < :current_time)'
            )->setParameter('current_time', \XLite\Base\SuperClass::getUserTime());
        }
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array|string               $value        Condition data
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndName(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if ($value) {
            $queryBuilder->andWhere('o.name like :name_pattern')
                ->setParameter('name_pattern', sprintf('%%%s%%', $value));
        }
    }

    /**
     * Get list of available OrderBy modes.
     *
     * @return array
     */
    protected function getOrderByModes()
    {
        return array(
            static::ORDER_BY_POSITION    => 'o.position',
            static::ORDER_BY_NAME        => 'o.name',
            static::ORDER_BY_ACTIVE_FROM => 'o.activeFrom',
            static::ORDER_BY_ACTIVE_TILL => 'o.activeTill',
        );
    }

    /**
     * Check whether it is an allowed Order By mode.
     *
     * @param string $mode
     *
     * @return boolean
     */
    protected function getOrderByField($mode)
    {
        $modes = $this->getOrderByModes();
        return isset($modes[$mode]) ? $modes[$mode] : null;
    }

}