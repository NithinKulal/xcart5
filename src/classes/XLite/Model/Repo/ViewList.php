<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * View list repository
 */
class ViewList extends \XLite\Model\Repo\ARepo
{
    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_INTERNAL;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = array(
        'weight' => true,
        'child'  => true,
        'tpl'    => true,
    );

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['class_list'] = array(
            static::ATTRS_CACHE_CELL => array('list', 'zone'),
        );

        return $list;
    }

    // {{{ Finders

    /**
     * Find class list
     *
     * @param string $list List name
     * @param string $zone Current interface name OPTIONAL
     *
     * @return array
     */
    public function findClassList($list, $zone = \XLite\Model\ViewList::INTERFACE_CUSTOMER)
    {
        $data = $this->getFromCache('class_list', array('list' => $list, 'zone' => $zone));

        if (!isset($data)) {
            $data = $this->retrieveClassList($list, $zone);
            $this->saveToCache($data, 'class_list', array('list' => $list, 'zone' => $zone));
        }

        return $data;
    }

    /**
     * Find actual (with empty version) by list name
     *
     * @param string $list List name
     *
     * @return array
     */
    public function findActualByList($list)
    {
        return $this->createQueryBuilder()
            ->where('v.list = :list AND v.version IS NOT NULL')
            ->setParameter('list', $list)
            ->getResult();
    }

    /**
     * Perform Class list query
     *
     * @param string $list List name
     * @param string $zone Current interface name
     *
     * @return array
     */
    public function retrieveClassList($list, $zone)
    {
        return $this->defineClassListQuery($list, $zone)->getResult();
    }

    /**
     * Find view list by tempalte pattern and list name
     *
     * @param string $tpl  Tempalte pattern
     * @param string $list List name
     *
     * @return \XLite\Model\ViewList|void
     */
    public function findOneByTplAndList($tpl, $list)
    {
        return $this->defineOneByTplAndListQuery($tpl, $list)->getSingleResult();
    }

    /**
     * Define query for findOneByTplAndList() method
     *
     * @param string $tpl  Tempalte pattern
     * @param string $list List name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByTplAndListQuery($tpl, $list)
    {
        return $this->createQueryBuilder()
            ->andWhere('v.twig LIKE :tpl AND v.list = :list')
            ->setParameter('tpl', $tpl)
            ->setParameter('list', $tpl);
    }

    /**
     * Define query builder for findClassList()
     *
     * @param string $list Class list name
     * @param string $zone Current interface name
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineClassListQuery($list, $zone)
    {
        return $this->createQueryBuilder()
            ->where('v.list = :list AND v.zone IN (:zone, :empty) AND v.version IS NULL')
            ->setParameter('empty', '')
            ->setParameter('list', $list)
            ->setParameter('zone', $zone);
    }

    // }}}

    // {{{ Operations

    /**
     * Delete obsolete view list childs
     * 
     * @param string $currentVersion Current version
     *  
     * @return void
     */
    public function deleteObsolete($currentVersion)
    {
        $this->defineDeleteObsoleteQuery($currentVersion)
            ->execute();
    }

    /**
     * Mark current view list childs as default
     *
     * @param string $currentVersion Current version
     *
     * @return void
     */
    public function markCurrentVersion($currentVersion)
    {
        $this->defineMarkCurrentVersionQuery($currentVersion)
            ->execute();
    }

    /**
     * Define query for deleteObsolete() method
     * 
     * @param string $currentVersion Current version
     *  
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineDeleteObsoleteQuery($currentVersion)
    {
        return $this->createPureQueryBuilder('v', false)
            ->delete($this->_entityName, 'v')
            ->andWhere('v.version != :version OR v.version IS NULL')
            ->setParameter('version', $currentVersion);
    }

    /**
     * Define query for markCurrentVersion() method
     *
     * @param string $currentVersion Current version
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineMarkCurrentVersionQuery($currentVersion)
    {
        return $this->createPureQueryBuilder('v', false)
            ->update($this->_entityName, 'v')
            ->set('v.version', 'NULL')
            ->andWhere('v.version = :version')
            ->setParameter('version', $currentVersion);
    }

    // }}}

}
