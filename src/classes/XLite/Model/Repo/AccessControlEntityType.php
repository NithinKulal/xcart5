<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Access control entity type repo
 */
class AccessControlEntityType extends \XLite\Model\Repo\ARepo
{
    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier = array(
        array('type'),
    );

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param string                     $value        Condition data
     *
     * @return void
     */
    protected function prepareCndType(\Doctrine\ORM\QueryBuilder $queryBuilder, $value)
    {
        if ($value) {
            $queryBuilder->andWhere($this->getMainAlias($queryBuilder) . '.type = :type')
                ->setParameter('type', $value);
        }
    }

    /**
     * Get access control type for entity
     *
     * @param \XLite\Model\AEntity $entity
     *
     * @return null
     */
    protected function getEntityType(\XLite\Model\AEntity $entity)
    {
        foreach ($this->findAllTypes() as $type) {
            if ($type->checkType($entity)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Find all Access control entity types
     *
     * @return array
     */
    public function findAllTypes()
    {
        $cnd = new \XLite\Core\CommonCell();
        return $this->search($cnd);
    }

    /**
     * Find one type by string type
     *
     * @param string $type
     *
     * @return mixed|null|object
     */
    public function findByType($type)
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->type = $type;
        $result = $this->search($cnd);

        return count($result) ? $result[0] : null;
    }
}