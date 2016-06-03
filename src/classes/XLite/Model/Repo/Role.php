<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Role repository 
 */
class Role extends \XLite\Model\Repo\Base\I18n
{
    /**
     * Find one role by permisssion code
     *
     * @param string $code Permission code
     *
     * @return \XLite\Model\Role
     */
    public function findOneByPermissionCode($code)
    {
        return $this->defineFindOneByPermissionCodeQuery($code)->getSingleResult();
    }

    /**
     * Find one role by name 
     * 
     * @param string $name Name
     *  
     * @return \XLite\Model\Role
     */
    public function findOneByName($name)
    {
        return $this->defineFindOneByNameQuery($name)->getSingleResult();
    }

    /**
     * Find one by record
     *
     * @param array                $data   Record
     * @param \XLite\Model\AEntity $parent Parent model OPTIONAL
     *
     * @return \XLite\Model\AEntity|void
     */
    public function findOneByRecord(array $data, \XLite\Model\AEntity $parent = null)
    {
        $model = parent::findOneByRecord($data, $parent);

        if (!$model && !empty($data['translations'])) {
            foreach ($data['translations'] as $translation) {
               $model = $this->findOneByName($translation['name']) ;
                if ($model) {
                    break;
                }
            }
        }

        return $model;
    }

    /**
     * Find one root-based role
     * 
     * @return \XLite\Model\Role
     */
    public function findOneRoot()
    {
        return $this->defineFindOneRootQuery()->getSingleResult();
    }

    /**
     * Define query for findOneByPermissionCode() method
     *
     * @param string $code Permission code
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByPermissionCodeQuery($code)
    {
        return $this->createQueryBuilder('r')
            ->linkInner('r.permissions')
            ->andWhere('permissions.code = :code')
            ->setParameter('code', $code)
            ->setMaxResults(1);
    }

    /**
     * Define query for findOneByName() method
     * 
     * @param string $name Name
     *  
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByNameQuery($name)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('translations.name = :name')
            ->setParameter('name', $name)
            ->setMaxResults(1);
    }

    /**
     * Define query for findOneRoot() method
     * 
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneRootQuery()
    {
        return $this->createQueryBuilder('r')
            ->linkInner('r.permissions')
            ->andWhere('permissions.code = :root')
            ->setMaxResults(1)
            ->setParameter('root', \XLite\Model\Role\Permission::ROOT_ACCESS);
    }
}
