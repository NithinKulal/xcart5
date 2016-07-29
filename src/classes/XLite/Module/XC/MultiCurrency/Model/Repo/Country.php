<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Model\Repo;

/**
 * Country
 */
class Country extends \XLite\Model\Repo\Country implements \XLite\Base\IDecorator
{
    const P_ORDER_BY_ACTIVE_CURRENCY = 'orderByActiveCurrency';

    const P_ACTIVE_CURRENCY = 'activeCurrency';
    const P_ENABLED = 'enabled';

    /**
     * Check if country has assigned currencies
     *
     * @param \XLite\MOdel\Country $country Country
     *
     * @return boolean
     */
    public function hasAssignedCurrencies($country)
    {
        if (isset($country)) {
            $count = $this->createPureQueryBuilder('c')
                ->select('COUNT (DISTINCT c.code)')
                ->innerJoin('c.active_currency', 'ac')
                ->andWhere('ac.enabled = :enabled')
                ->andWhere('c.code = :country_code')
                ->setParameter('country_code', $country->getCode())
                ->setParameter('enabled', true)
                ->getSingleScalarResult();
        } else {
            $count = 0;
        }

        return $count > 0;
    }

    /**
     * Get all available countries as array
     *
     * @return array
     */
    public function getAllAvailableCountriesAsArray()
    {
        $qb = $this->createPureQueryBuilder('c')
            ->select('c', 'IFNULL(tr.country, trd.country) AS country')
            ->innerJoin(
                'c.translations',
                'trd',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'trd.code = :default_code'
            )
            ->leftJoin(
                'c.translations',
                'tr',
                \Doctrine\ORM\Query\Expr\Join::WITH,
                'tr.code = :code'
            )
            ->andWhere('c.enabled = :is_enabled')
            ->orderBy('country', 'ASC')
            ->setParameter('is_enabled', true)
            ->setParameter('code', $this->getTranslationCode())
            ->setParameter('default_code', \XLite::getDefaultLanguage());

        $countries = $qb->getQuery()->getScalarResult();

        $_tmp = array();
        foreach ($countries as $i => $country) {
            $_tmp[$i] = array();

            foreach ($country as $field => $value) {
                if (strpos($field, 'c_') === 0) {
                    $_tmp[$i][str_replace('c_', '', $field)] = $value;
                } else {
                    $_tmp[$i][$field] = $value;
                }
            }
        }
        $countries = $_tmp;

        return $countries;
    }

    /**
     * Get available countries ids for active currency
     *
     * @param integer $activeCurrencyId Active currency id
     *
     * @return array
     */
    public function getActiveCurrencyAvailableCountries($activeCurrencyId)
    {
        $qb = $this->createQueryBuilder();

        $countryInactive = $qb->expr()->orX(
            ':active_currency <> c.active_currency',
            'c.active_currency is NULL'
        );

        return $qb->andWhere($countryInactive)
            ->setParameter('active_currency', $activeCurrencyId)
            ->getResult();
    }

    /**
     *  Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param integer                    $value        Active currency ID
     *
     * @return void
     */
    protected function prepareCndActiveCurrency(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder
            ->andWhere('c.active_currency = :active_currency_id')
            ->setParameter('active_currency_id', $value);
    }

    /**
     *  Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param integer                    $value        Active currency ID
     * @param boolean                    $countOnly    "Count only" flag. Do not need to add "order by" clauses if only count is needed.
     *
     * @return void
     */
    protected function prepareCndOrderByActiveCurrency(\Doctrine\ORM\QueryBuilder $queryBuilder, $value, $countOnly)
    {
        if (!$countOnly) {
            $queryBuilder->addOrderBy('c.active_currency', $value);
        }
    }

    /**
     *  Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param integer                    $value        Active currency ID
     *
     * @return void
     */
    protected function prepareCndEnabled(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        $queryBuilder->andWhere('c.enabled = :is_enabled')
            ->setParameter('is_enabled', (boolean) $value);
    }
}
