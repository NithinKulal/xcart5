<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Country repository
 */
class State extends \XLite\Model\Repo\ARepo
{
    /**
     * Allowable search params
     */
    
    const P_SUBSTRING       = 'substring';
    const P_COUNTRY_CODE    = 'countryCode';

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SERVICE;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'state';

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('code', 'country_code'),
    );

    /**
     * Get dump 'Other' state
     *
     * @param string $customState Custom state name OPTIONAL
     *
     * @return \XLite\Model\State
     */
    public function getOtherState($customState = null)
    {
        $state = new \XLite\Model\State();
        $state->state = isset($customState) ? $customState : 'Other';
        $state->state_id = -1;

        return $state;
    }

    /**
     * Check - is state id of dump 'Other' state or not
     *
     * @param integer $stateId State id
     *
     * @return boolean
     */
    public function isOtherStateId($stateId)
    {
        return -1 == $stateId;
    }

    /**
     * Get state code by state id
     *
     * @param integer $stateId State id
     *
     * @return string|void
     */
    public function getCodeById($stateId)
    {
        $result = $this->getFromCache('codes', array('state_id' => $stateId));

        if (!isset($result)) {
            $entity = $this->defineGetCodeByIdQuery($stateId)->getSingleResult();
            $result = $entity ? $entity->getCode() : '';

            $this->saveToCache($result, 'codes', array('state_id' => $stateId));
        }

        return $result;
    }

    /**
     * Find state by id (dump 'Other' state included)
     *
     * @param integer $stateId     State id
     * @param string  $customState Custom state name if state is dump 'Other' state OPTIONAL
     *
     * @return \XLite\Model\State
     */
    public function findById($stateId, $customState = '')
    {
        return $this->isOtherStateId($stateId)
            ? $this->getOtherState($customState)
            : $this->findOneByStateId($stateId);
    }

    /**
     * Find state by id
     *
     * @param integer $stateId State id
     *
     * @return \XLite\Model\State
     */
    public function findOneByStateId($stateId)
    {
        return $this->defineOneByStateIdQuery($stateId)->getSingleResult();
    }

    /**
     * Find all states
     *
     * @return array
     */
    public function findAllStates()
    {
        $data = $this->getFromCache('all');

        if (!isset($data)) {
            $data = $this->defineAllStatesQuery()->getResult();
            $this->saveToCache($data, 'all');
        }

        return $data;
    }

    /**
     * Find all states grouped
     *
     * @return array
     */
    public function findAllStatesGrouped()
    {
        $data = $this->getFromCache('allGrouped');

        if (!isset($data)) {
            $data = $this->getGroupedByRegion(
                $this->defineAllStatesQuery()->getResult()
            );
            $this->saveToCache($data, 'allGrouped');
        }

        return $data;
    }

    /**
     * Find states by country code
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
     * Find states by country code and by state code or state name
     *
     * @param string $countryCode Country code
     * @param string $code        State code or name
     *
     * @return \XLite\Model\State|void
     */
    public function findOneByCountryAndState($countryCode, $code)
    {
        return $this->defineOneByCountryAndStateQuery($countryCode, $code)->getSingleResult();
    }

    /**
     * Find states by country code and state code
     *
     * @param string $countryCode Country code
     * @param string $code        State code
     *
     * @return \XLite\Model\State|void
     */
    public function findOneByCountryAndCode($countryCode, $code)
    {
        return $this->defineOneByCountryAndCodeQuery($countryCode, $code)->getSingleResult();
    }

    /**
     * Find states by country code and region
     *
     * @param string $countryCode Country code
     * @param string $region Region code
     * 
     * @return \XLite\Model\State|void
     */
    public function findByCountryCodeGroupedByRegion($countryCode)
    {
        return $this->getGroupedByRegion(
            $this->findByCountryCode($countryCode)
        );
    }

    /**
     * Find one by record
     *
     * @param array                $data   Record
     * @param \XLite\Model\AEntity $parent Parent model OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function findOneByRecord(array $data, \XLite\Model\AEntity $parent = null)
    {
        if (!empty($data['country_code']) && !empty($data['code'])) {
            $result = $this->findOneByCountryAndCode($data['country_code'], $data['code']);

        } elseif ($parent && $parent instanceOf \XLite\Model\Country) {
            $result = $this->findOneByCountryAndCode($parent->getCode(), $data['code']);

        } elseif (!empty($data['code']) && !empty($data['country']) && is_array($data['country']) && !empty($data['country']['code'])) {
            $result = $this->findOneByCountryAndCode($data['country']['code'], $data['code']);

        } else {
            $result = parent::findOneByRecord($data, $parent);
        }

        return $result;
    }

    /**
     * Grouping states by region
     * 
     * @param array $states States to group
     * 
     * @return array
     */
    protected function getGroupedByRegion(array $states)
    {
        $regions = array();

        foreach ($states as $state) {
            if (null === $state->getRegion()) {
                $regions[] = $state;
            }else{
                $code = $state->getRegion()->getCode();
                if (isset($regions[$code])) {
                    $regions[$code]['options'][] = $state;
                }else{
                    $regions[$code] = array(
                        'label'     => $state->getRegion()->getName(),
                        'options'   => array($state)
                    );
                }
            }
        }
        return $regions;
    }

    /**
     * Define query builder for getCodeById() method
     *
     * @param integer $stateId State id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineGetCodeByIdQuery($stateId)
    {
        return $this->createQueryBuilder()
            ->where('s.state_id = :id')
            ->setMaxResults(1)
            ->setParameter('id', $stateId);
    }

    /**
     * Define query builder for findOneByStateId()
     *
     * @param integer $stateId State id
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByStateIdQuery($stateId)
    {
        return $this->createQueryBuilder()
            ->addSelect('c')
            ->leftJoin('s.country', 'c')
            ->andWhere('s.state_id = :id')
            ->setParameter('id', $stateId)
            ->setMaxResults(1);
    }

    /**
     * Define query builder for findAllStates()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllStatesQuery()
    {
        return $this->createQueryBuilder()
            ->addSelect('c')
            ->leftJoin('s.country', 'c');
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
            ->andWhere('s.country = :country')
            ->setParameter('country', $country);
    }

    /**
     * Define query for findOneByCountryAndCode() method
     *
     * @param string $countryCode Country code
     * @param string $code        State code
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByCountryAndCodeQuery($countryCode, $code)
    {
        return $this->createQueryBuilder()
            ->innerJoin('s.country', 'country')
            ->andWhere('country.code = :country AND s.code = :code')
            ->setParameter('country', $countryCode)
            ->setParameter('code', $code);
    }

    /**
     * Define query for findOneByCountryAndState() method
     *
     * @param string $countryCode Country code
     * @param string $code        State code or name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByCountryAndStateQuery($countryCode, $code)
    {
        $orCnd = new \Doctrine\ORM\Query\Expr\Orx();
                $orCnd->add('s.code = :stateCode');
                $orCnd->add('s.state = :stateCode');

        return $this->createQueryBuilder()
            ->innerJoin('s.country', 'country')
            ->andWhere('country.code = :country')
            ->andWhere($orCnd)
            ->setParameter('country', $countryCode)
            ->setParameter('stateCode', $code);
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

        $list['allGrouped'] = array(
            self::RELATION_CACHE_CELL => array('\XLite\Model\Country'),
        );

        $list['codes'] = array(
            self::ATTRS_CACHE_CELL => array('state_id'),
        );

        return $list;
    }

    // }}}

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Profile
     *
     * @return void
     */
    protected function prepareCndCountryCode(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {

            $queryBuilder->innerJoin('s.country', 'c')
                ->andWhere('c.code = :countryCode')
                ->setParameter('countryCode', $value);
        }
    }


    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param \XLite\Model\Profile       $value        Profile
     *
     * @return void
     */
    protected function prepareCndSubstring(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if (!empty($value)) {

            $queryBuilder->andWhere('s.state LIKE :substring')
                ->setParameter('substring', '%' . $value . '%');
        }
    }

    // }}}
}
