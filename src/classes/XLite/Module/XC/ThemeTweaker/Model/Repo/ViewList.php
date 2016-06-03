<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Model\Repo;

/**
 * View list repository
 */
class ViewList extends \XLite\Model\Repo\ViewList implements \XLite\Base\IDecorator
{
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
        $data = parent::retrieveClassList($list, $zone);

        usort(
            $data,
            function ($a, $b) {
                $weight_a = $a->getWeightActual();
                $weight_b = $b->getWeightActual();

                if ($weight_a == $weight_b) {
                    return 0;
                }
                return ($weight_a < $weight_b) ? -1 : 1;
            }
        );

        $data = array_filter(
            $data,
            function ($item) use ($list) {
                return $list === $item->getListActual();
            }
        );

        return $data;
    }

    /**
     * Define query builder for findClassList()
     *
     * @param array $changeset Array of change records
     *
     * @return void
     */
    public function updateOverrides($changeset)
    {
        if ($changeset) {
            foreach ($changeset as $change) {
                $entity = $this->find($change['id']);
                if ($entity) {
                    $entity->setOverrideMode($change['mode']);

                    if (isset($change['list'])) {
                        $entity->setListOverride($change['list']);
                    }

                    if (isset($change['weight'])) {
                        $entity->setWeightOverride($change['weight']);
                    }
                }
            }

            $this->cleanCache();

            \XLite\Core\Database::getEM()->flush();
        }
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
            ->where('(v.list = :list OR v.list_override = :list) AND v.zone IN (:zone, :empty) AND v.version IS NULL')
            ->setParameter('empty', '')
            ->setParameter('list', $list)
            ->setParameter('zone', $zone);
    }

    /**
     * Find overridden view list items
     *
     * @return array
     */
    public function findOverridden()
    {
        $query = $this->createQueryBuilder()
             ->where('v.override_mode > :off_mode')
             ->andWhere('v.version IS NULL')
             ->setParameter('off_mode', \XLite\Model\ViewList::OVERRIDE_OFF);

        return $query->getResult();
    }

    /**
     * Find first entity equal to $other
     *
     * @param \XLite\Model\ViewList $other Other entity
     * @param boolean $versioned Add `version is not null` condition
     *
     * @return \XLite\Model\ViewList|null
     */
    public function findEqual(\XLite\Model\ViewList $other, $versioned = false)
    {
        if (!$other) {
            return null;
        }

        $conditions = array(
            'list' => $other->getList(),
            'child' => $other->getChild(),
            'tpl' => $other->getTpl(),
            'zone' => $other->getZone(),
            'weight' => $other->getWeight(),
        );

        $qb = $this->createQueryBuilder()
            ->andWhere('v.list = :list AND v.child = :child AND v.tpl = :tpl AND v.zone = :zone AND v.weight = :weight')
            ->setParameters($conditions);

        if ($versioned) {
            $qb->andWhere('v.version IS NOT NULL');
        }

        return $qb->getSingleResult();
    }

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
        return \XLite\Core\Request::getInstance()->isInLayoutMode()
            ? $data = $data = $this->retrieveClassList($list, $zone)
            : parent::findClassList($list, $zone);
    }
}
