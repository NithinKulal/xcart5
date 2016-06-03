<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Query;

/**
 * EntityTypeVersion repository
 */
class EntityTypeVersion extends \XLite\Model\Repo\ARepo implements EventSubscriber
{
    /** @var string[] */
    protected $entityTypeVersions;

    /**
     * Entity classes that were flushed during the previous EntityManager#flush() method call. To be used in a postFlush event handler to bump entity type versions.
     *
     * @var array
     */
    protected $flushedEntityTypes = [];

    /**
     * Entity types which versions were bumped
     *
     * @var array
     */
    protected $bumpedEntityTypes = [];

    /**
     * Get entity type version UUID for the specified entity type
     *
     * @param $entityType
     *
     * @return null|string
     */
    public function getEntityTypeVersion($entityType)
    {
        if (!isset($this->entityTypeVersions)) {
            $this->entityTypeVersions = $this->fetchAllEntityTypeVersions();
        }

        return isset($this->entityTypeVersions[$entityType]) ? $this->entityTypeVersions[$entityType] : null;
    }

    /**
     * Bump version for a specific entity type
     *
     * @param $entityType
     */
    public function bumpEntityTypeVersion($entityType)
    {
        $newVersion = $this->generateUUIDv4();

        $em   = $this->getEntityManager();
        $conn = $em->getConnection();

        $tableName = $em->getClassMetadata('XLite\Model\EntityTypeVersion')->getTableName();

        $numAffectedRows = $conn->update($tableName, ['version' => $newVersion], ['entityType' => $entityType]);

        if ($numAffectedRows == 0) {
            try {
                $conn->insert($tableName, ['version' => $newVersion, 'entityType' => $entityType]);
            } catch (DBALException $e) {
                // Execute an update if someone else performed insert before we did:
                $conn->update($tableName, ['version' => $newVersion], ['entityType' => $entityType]);
            }
        }

        if (isset($this->entityTypeVersions)) {
            $this->entityTypeVersions[$entityType] = $newVersion;
        }

        $this->bumpedEntityTypes[$entityType] = $entityType;
    }

    /**
     * Get entity types which versions were bumped.
     *
     * @return array
     */
    public function getBumpedEntityTypes()
    {
        return array_values($this->bumpedEntityTypes);
    }

    /**
     * Bump versions for multiple entity types
     *
     * @param $entities
     */
    protected function bumpEntityTypeVersions($entities)
    {
        foreach ($entities as $entity) {
            $this->bumpEntityTypeVersion($entity);
        }
    }

    protected function fetchAllEntityTypeVersions()
    {
        $entityTypeVersions = [];

        $evs = $this->createQueryBuilder('ev')
            ->select('ev')
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        foreach ($evs as $ev) {
            $entityTypeVersions[$ev['entityType']] = $ev['version'];
        }

        return $entityTypeVersions;
    }

    protected function generateUUIDv4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            Events::onFlush,
            Events::postFlush,
        ];
    }

    /**
     * onFlush event handler
     *
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $event->getEntityManager();
        /* @var $uow \Doctrine\ORM\UnitOfWork */
        $uow = $em->getUnitOfWork();

        $types = [];

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $type = ClassUtils::getClass($entity);

            if (!isset($types[$type])) {
                $types[$type] = $type;
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            $type = ClassUtils::getClass($entity);

            if (!isset($types[$type])) {
                $types[$type] = $type;
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            $type = ClassUtils::getClass($entity);

            if (!isset($types[$type])) {
                $types[$type] = $type;
            }
        }

        unset($types['XLite\Model\EntityTypeVersion']);

        foreach ($this->getNotTrackedEntityTypes() as $type) {
            unset($types[$type]);
        }

        $this->flushedEntityTypes = $types;
    }

    /**
     * postFlush event handler
     *
     * @param PostFlushEventArgs $event
     */
    public function postFlush(PostFlushEventArgs $event)
    {
        $this->bumpEntityTypeVersions($this->flushedEntityTypes);
    }

    /**
     * Get (service) entity types that will not be tracked for changes
     *
     * @return array
     */
    protected function getNotTrackedEntityTypes()
    {
        return [
            'XLite\Model\Session',
            'XLite\Model\SessionCell',
            'XLite\Model\FormId',
        ];
    }
}
