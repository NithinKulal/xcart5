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
    const SEARCH_ENABLED          = 'enabled';
    const SEARCH_NAME             = 'name';
    const SEARCH_ACTIVE           = 'active';
    const SEARCH_VISIBLE_HOME     = 'visibleHome';
    const SEARCH_VISIBLE_OFFERS   = 'visibleOffers';

    /**
     *
     * Allowed sort criteria
     */
    const ORDER_BY_POSITION    = 's.position';
    const ORDER_BY_NAME        = 's.name';
    const ORDER_BY_ACTIVE_FROM = 's.activeFrom';
    const ORDER_BY_ACTIVE_TILL = 's.activeTill';

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
        $cnd->{static::P_ORDER_BY} = array(static::ORDER_BY_POSITION, 'ASC');
        
        return $cnd;
    }

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from.
     * @param string $code    Language code OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null, $code = null)
    {
        return parent::createQueryBuilder($alias, $indexBy, $code)
            ->linkInner('s.offerType', 't')
            ->addSelect('t');
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
            $queryBuilder->andWhere($value ? 's.enabled <> 0' : 's.enabled = 0');
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
            $queryBuilder->andWhere($value ? 's.promoHome <> 0' : 's.promoHome = 0');
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
            $queryBuilder->andWhere($value ? 's.promoOffers <> 0' : 's.promoOffers = 0');
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
                    ? '((s.activeFrom = 0) OR (s.activeFrom <= :current_time)) AND ((s.activeTill = 0) OR (s.activeTill >= :current_time))'
                    : '(s.activeFrom > :current_time) OR (s.activeTill < :current_time)'
            )->setParameter('current_time', \XLite\Core\Converter::time());
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
            $queryBuilder->andWhere('s.name like :name_pattern')
                ->setParameter('name_pattern', sprintf('%%%s%%', $value));
        }
    }

}