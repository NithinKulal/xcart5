<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model\EntityVersion;

use PDO;
use XLite\Core\Database;

/**
 * BulkEntityVersionFetcher prefetches multiple entity versions at once in a single query to allow subsequent fetch operations to get results from $fetchedVersions cache.
 * Entity versions will also be fetched from unit of work's identity map, if corresponding entities are already in an identity map.
 */
class BulkEntityVersionFetcher implements \Serializable
{
    /** @var array */
    private static $fetchedVersions = [];

    /** @var string */
    protected $entityType;

    /** @var string */
    protected $rootEntityType;

    /** @var string */
    protected $entityTableName;

    /** @var string */
    protected $identifierFieldName;

    /** @var array */
    private $fetchIdsInBulk;

    public function __construct($entityType, array $fetchIdsInBulk)
    {
        $this->entityType     = $entityType;
        $this->fetchIdsInBulk = $fetchIdsInBulk;

        $meta = Database::getEM()->getClassMetadata($entityType);

        $this->rootEntityType      = $meta->rootEntityName;
        $this->entityTableName     = $meta->getTableName();
        $this->identifierFieldName = $meta->getSingleIdentifierFieldName();
    }

    public function fetch($id)
    {
        if (isset(self::$fetchedVersions[$this->entityType . $id])) {
            return self::$fetchedVersions[$this->entityType . $id];
        }

        $em = Database::getEM();

        $prefetchIds = array_flip(array_diff_key(array_flip($this->fetchIdsInBulk), self::$fetchedVersions));

        if (!in_array($id, $prefetchIds)) {
            $prefetchIds[] = $id;
        }

        foreach ($prefetchIds as $k => $pid) {
            if (($entity = $em->getUnitOfWork()->tryGetById($pid, $this->rootEntityType)) !== false) {
                self::$fetchedVersions[$this->entityType . $pid] = $entity->getEntityVersion();

                unset($prefetchIds[$k]);
            }
        }

        if (isset(self::$fetchedVersions[$this->entityType . $id])) {
            return self::$fetchedVersions[$this->entityType . $id];
        }

        $stmt = $em->getConnection()->prepare("SELECT {$this->identifierFieldName} AS id, entityVersion FROM {$this->entityTableName} WHERE {$this->identifierFieldName} IN (" . implode(',', array_fill(0, count($prefetchIds), '?')) . ")");

        foreach (array_values($prefetchIds) as $i => $pid) {
            $stmt->bindValue($i + 1, $pid, PDO::PARAM_INT);
        }

        $stmt->execute();

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $result) {
            self::$fetchedVersions[$this->entityType . $result['id']] = $result['entityVersion'];
        }

        return self::$fetchedVersions[$this->entityType . $id];
    }

    /**
     * String representation of object
     * @link  http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([
            $this->entityType,
            $this->fetchIdsInBulk,
            $this->rootEntityType,
            $this->entityTableName,
            $this->identifierFieldName,
        ]);
    }

    /**
     * Constructs the object
     * @link  http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list (
            $this->entityType,
            $this->fetchIdsInBulk,
            $this->rootEntityType,
            $this->entityTableName,
            $this->identifierFieldName
        ) = unserialize($serialized);
    }
}