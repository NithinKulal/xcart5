<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Region repository
 */
class Region extends \XLite\Model\Repo\ARepo
{
    /**
     * Find all regions
     *
     * @return array
     */
    public function findAllRegions()
    {
        $data = $this->getFromCache('all');

        if (!isset($data)) {
            $data = $this->defineAllRegionsQuery()->getResult();
            $this->saveToCache($data, 'all');
        }

        return $data;
    }

    /**
     * Find regions by country code
     *
     * @param string $countryCode Country code
     *
     * @return \XLite\Model\State|void
     */
    public function findByCountryCode($countryCode)
    {
        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($countryCode);

        return $country ? $this->defineByCountryQuery($country)->getResult() : array();
    }

    /**
     * Find region by code 
     *
     * @param string $code     Region code
     *
     * @return \XLite\Model\Region
     */
    public function findByCode($code)
    {
        return $this->defineOneByCodeQuery($code)->getSingleResult();
    }

    /**
     * Define query builder for findAllRegions()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllRegionsQuery()
    {
        return $this->createQueryBuilder()
            ->addSelect('c')
            ->leftJoin('r.country', 'c');
    }

    /**
     * Define query for findByCountryCode() method
     *
     * @param \XLite\Model\Country $country Country
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineByCountryQuery(\XLite\Model\Country $country)
    {
        return $this->createQueryBuilder()
            ->andWhere('r.country = :country')
            ->setParameter('country', $country);
    }

    /**
     * Define query builder for findOneByCode()
     *
     * @param string $code Region Code
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByCodeQuery($code)
    {
        return $this->createQueryBuilder()
            ->addSelect('c')
            ->leftJoin('r.country', 'c')
            ->andWhere('r.code = :code')
            ->setParameter('code', $code)
            ->setMaxResults(1);
    }

    // {{{ Cache

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all'] = array(
            self::RELATION_CACHE_CELL => array('\XLite\Model\Country'),
        );

        $list['codes'] = array(
            self::ATTRS_CACHE_CELL => array('code'),
        );

        return $list;
    }

    // }}}
}
