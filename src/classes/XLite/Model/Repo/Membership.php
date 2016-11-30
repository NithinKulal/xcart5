<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Membership repository
 */
class Membership extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Allowable search params
     */

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_SECONDARY;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy = 'position';

    // {{{ Search

    // }}}

    // {{{ defineCacheCells

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        $list = parent::defineCacheCells();

        $list['all'] = array();

        $list['enabled'] = array(
            self::ATTRS_CACHE_CELL => array('enabled'),
        );

        return $list;
    }

    // }}}

    // {{{ findAllMemberships

    /**
     * Find all languages
     *
     * @return array
     */
    public function findAllMemberships()
    {
        return $this->defineAllMembershipsQuery()->getResult();
    }

    /**
     * Define query builder for findAllMemberships()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineAllMembershipsQuery()
    {
        return $this->createQueryBuilder();
    }

    // }}}

    // {{{ findActiveMemberships

    /**
     * Find all enabled languages
     *
     * @return array
     */
    public function findActiveMemberships()
    {
        return $this->defineActiveMembershipsQuery()->getResult();
    }

    /**
     * Define query builder for findActiveMemberships()
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineActiveMembershipsQuery()
    {
        return $this->createQueryBuilder()
            ->where('m.enabled = :true')
            ->setParameter('true', true);
    }

    // }}}

    // {{{ findOneByName

    /**
     * Find membership by name (any language)
     *
     * @param string  $name       Name
     * @param boolean $onlyActive Search only in enabled mebmerships OPTIONAL
     * @param boolean $countOnly  Count only OPTIONAL
     *
     * @return \XLite\Model\Membership|void
     */
    public function findOneByName($name, $onlyActive = true, $countOnly = false)
    {
        return $countOnly
            ? count($this->defineOneByNameQuery($name, $onlyActive)->getResult())
            : $this->defineOneByNameQuery($name, $onlyActive)->getSingleResult();
    }

    /**
     * Define query builder for findOneByName() method
     *
     * @param string  $name       Name
     * @param boolean $onlyActive Search only in enabled mebmerships
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineOneByNameQuery($name, $onlyActive)
    {
        $qb = $this->createQueryBuilder()
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1);

        if ($onlyActive) {
            $qb->andWhere('m.enabled = :true');
            $qb->setParameter('true', true);
        }

        return $qb;
    }

    // }}}

    /**
     * Delete single entity
     *
     * @param \XLite\Model\AEntity $entity Entity to detach
     *
     * @return void
     */
    protected function performDelete(\XLite\Model\AEntity $entity)
    {
        $alias = 'qd';
        $qb = \XLite\Core\Database::getEM()->createQueryBuilder();
        $qb->delete('XLite\Model\QuickData', $alias)
            ->andWhere($qb->expr()->eq("{$alias}.membership", ':membership'))
            ->setParameter('membership', $entity);
        $qb->getQuery()->getResult();

        parent::performDelete($entity);
    }

    /**
     * Insert single entity
     *
     * @param \XLite\Model\AEntity|array $entity Data to insert OPTIONAL
     *
     * @return void
     */
    protected function performInsert($entity = null)
    {
        $entity = parent::performInsert($entity);

        if ($entity && !\XLite\Core\Database::getRepo('XLite\Model\Product')->getBlockQuickDataFlag()) {
            \XLite\Core\QuickData::getInstance()->updateMembershipData($entity);
        }

        return $entity;
    }
}
