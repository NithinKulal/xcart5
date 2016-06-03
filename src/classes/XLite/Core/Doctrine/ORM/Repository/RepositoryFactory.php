<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Doctrine\ORM\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Includes\Autoloader;
use Includes\Utils\Converter;

/**
 * Custom repository factory
 */
class RepositoryFactory implements \Doctrine\ORM\Repository\RepositoryFactory
{
    /**
     * The list of EntityRepository instances.
     *
     * @var \Doctrine\Common\Persistence\ObjectRepository[]
     */
    private $repositoryList = array();

    /**
     * {@inheritdoc}
     */
    public function getRepository(EntityManagerInterface $entityManager, $entityName)
    {
        $repositoryHash = $entityManager->getClassMetadata($entityName)->getName() . spl_object_hash($entityManager);

        if (isset($this->repositoryList[$repositoryHash])) {
            return $this->repositoryList[$repositoryHash];
        }

        return $this->repositoryList[$repositoryHash] = $this->createRepository($entityManager, $entityName);
    }

    /**
     * Create a new repository instance for an entity class.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager The EntityManager instance.
     * @param string                               $entityName    The name of the entity.
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    private function createRepository(EntityManagerInterface $entityManager, $entityName)
    {
        /* @var $metadata \Doctrine\ORM\Mapping\ClassMetadata */
        $metadata            = $entityManager->getClassMetadata($entityName);
        $repositoryClassName = $metadata->customRepositoryClassName
            ?: $this->getDefaultRepositoryClassName($entityName);

        return new $repositoryClassName($entityManager, $metadata);
    }

    private function getDefaultRepositoryClassName($entityName)
    {
        $entityClass = \Doctrine\Common\Util\ClassUtils::getRealClass($entityName);

        $repoClassName = Converter::prepareClassName(str_replace('\Model\\', '\Model\Repo\\', $entityClass), false);

        return Autoloader::checkAutoload($repoClassName)
            ? $repoClassName
            : '\XLite\Model\Repo\Base\\' . (preg_match('/\wTranslation$/Ss', $entityClass) ? 'Translation' : 'Common');
    }
}