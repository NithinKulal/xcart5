<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

use Doctrine\DBAL\Schema\ColumnDiff;
use Doctrine\DBAL\Schema\SchemaDiff;
use Doctrine\ORM\Query\Expr;

/**
 * Abstract repository
 */
abstract class ARepo extends \Doctrine\ORM\EntityRepository
{
    /**
     * Cache default TTL (1 year)
     */
    const CACHE_DEFAULT_TTL = 2592000;

    /**
     * Cache cell fields names
     */
    const KEY_TYPE_CACHE_CELL  = 'keyType';
    const ATTRS_CACHE_CELL     = 'attrs';
    const RELATION_CACHE_CELL  = 'relation';
    const CONVERTER_CACHE_CELL = 'converter';
    const GENERATOR_CACHE_CELL = 'generator';

    /**
     * Cache key types
     */
    const CACHE_ATTR_KEY   = 'attributesKey';
    const CACHE_HASH_KEY   = 'hashKey';
    const CACHE_CUSTOM_KEY = 'customKey';

    const DEFAULT_KEY_TYPE = self::CACHE_ATTR_KEY;
    const EMPTY_CACHE_CELL = 'all';

    /**
     * Repository type codes
     */
    const TYPE_STORE     = 'store';
    const TYPE_SECONDARY = 'secondary';
    const TYPE_SERVICE   = 'service';
    const TYPE_INTERNAL  = 'internal';

    /**
     * Default value for the "flush" param
     */
    const FLUSH_BY_DEFAULT = true;

    /**
     * Name of the prefix for the calculated field
     */
    const CALCULATED_FIELD_FLAG = 'calculatedField.';
    const CALCULATED_FIELD_ALIAS_FLAG = 'calculatedFieldAlias.';

    /**
     * Search conditions
     */
    const P_LIMIT       = 'limit';
    const P_ORDER_BY    = 'orderBy';

    /**
     * Search types
     */
    const SEARCH_MODE_COUNT     = 'count';
    const SEARCH_MODE_ENTITIES  = 'entities';
    const SEARCH_MODE_INDEXED   = 'searchModeIndexed';
    const SEARCH_MODE_IDS       = 'ids';

    /**
     * Any search mode alias for excluding contidions
     */
    const EXCLUDE_FROM_ANY = 'excludeFromAny';

    /**
     * Cache driver
     *
     * @var \XLite\Core\Cache\Registry
     */
    protected static $cacheDriver;

    /**
     * Cache cells (local cache)
     *
     * @var array
     */
    protected $cacheCells;

    /**
     * Default 'order by' field name
     *
     * @var string
     */
    protected $defaultOrderBy;

    /**
     * Default model alias
     *
     * @var string
     */
    protected $defaultAlias;

    /**
     * Alternative record identifiers
     *
     * @var array
     */
    protected $alternativeIdentifier;

    /**
     * Entity properties
     *
     * @var array
     */
    protected $entityProperties;

    /**
     * Flush unit-of-work changes after every record loading
     *
     * @var boolean
     */
    protected $flushAfterLoading = false;

    /**
     * Repository type
     *
     * @var string
     */
    protected $type = self::TYPE_STORE;

    /**
     * Query builder class
     *
     * @var string
     */
    protected $queryBuilderClass;

    /**
     * Identifiers of exported entities
     *
     * @var array
     */
    protected $exportSelection = array();

    /**
     * Current search condition
     *
     * @var array
     */
    protected $searchState = array();

    /**
     * Has applied filter
     *
     * @var boolean
     */
    protected $hasFilter = false;

    /**
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param EntityManager                         $em    The EntityManager to use.
     * @param \Doctrine\ORM\Mapping\ClassMetadata   $class The class descriptor.
     */
    public function __construct($em, \Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);

        $this->clearSearchState();
    }

    protected function clearSearchState()
    {
        $this->searchState = array(
            'currentSearchCnd'  => null,
            'queryBuilder'      => null,
            'searchMode'        => static::SEARCH_MODE_ENTITIES,
            'parts'             => array(),
            'parameters'        => array(),
            'joins'             => array(),
        );
    }

    /**
     * Get repository type
     *
     * @return string
     */
    public function getRepoType()
    {
        return $this->type;
    }

    /**
     * Add cache relations
     *
     * @param array $externalCells External cells
     *
     * @return void
     */
    public function addCacheRelations(array $externalCells)
    {
        $key = $this->getHashPrefix('externalCells');

        $cacheCells = \XLite\Model\Repo\ARepo::getCacheDriver()->fetch($key);
        if (!is_array($cacheCells)) {
            $cacheCells = array();
        }

        foreach ($externalCells as $model => $cells) {
            $cacheCells[$model] = isset($cacheCells[$model])
                ? array_merge($cacheCells[$model], $cells)
                : $cells;
        }

        \XLite\Model\Repo\ARepo::getCacheDriver()->save($key, $cacheCells, static::CACHE_DEFAULT_TTL);
    }

    /**
     * Get related cache cells
     *
     * @return array
     */
    public function getRelatedCacheCells()
    {
        $cacheCells = \XLite\Model\Repo\ARepo::getCacheDriver()->fetch(
            $this->getHashPrefix('externalCells')
        );

        return is_array($cacheCells) ? $cacheCells : array();
    }

    /**
     * Check - has repository any cache cells or not
     *
     * @return boolean
     */
    public function hasCacheCells()
    {
        return $this->getCacheCells();
    }

    /**
     * Delete cache by entity
     *
     * @param \XLite\Model\AEntity $entity Record
     *
     * @return void
     */
    public function deleteCacheByEntity(\XLite\Model\AEntity $entity)
    {
        foreach ($this->getCacheCells() as $name => $cell) {
            // Get cell arguments
            if ($cell[self::CONVERTER_CACHE_CELL]) {
                   $params = $this->{$cell[self::CONVERTER_CACHE_CELL]}($entity);

            } elseif (is_array($cell[self::ATTRS_CACHE_CELL])
                && $cell[self::ATTRS_CACHE_CELL]
            ) {
                $params = array();
                foreach ($cell[self::ATTRS_CACHE_CELL] as $key) {
                    $params[$key] = $entity->{'get' . \XLite\Core\Converter::convertToCamelCase($key)}();
                }

            } else {
                $params = array();
            }

            // Delete cell
            \XLite\Model\Repo\ARepo::getCacheDriver()->delete(
                $this->getCellHash($name, $cell, $params)
            );
        }

        // Delete related cache cells
        foreach ($this->getRelatedCacheCells() as $model => $cells) {
            $repo = \XLite\Core\Database::getRepo($model);
            foreach ($cells as $cell) {
                $repo->deleteCache($cell);
            }
        }
    }

    /**
     * Delete repository cache or cell cache
     *
     * @param string $name Cell name OPTIONAL
     *
     * @return void
     */
    public function deleteCache($name = '')
    {
        $prefix = $this->getHashPrefix() . '.' . $name;
        foreach (\XLite\Model\Repo\ARepo::getCacheDriver()->getIds() as $id) {
            if (0 === strpos($id, $prefix)) {
                \XLite\Model\Repo\ARepo::getCacheDriver()->delete($id);
            }
        }
    }

    /**
     * Assign default orderBy
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     * @param string                     $alias        Table short alias in query builder OPTIONAL
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function assignDefaultOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, $alias = null)
    {
        if ($this->defaultOrderBy) {
            if (null === $alias) {
                $alias = $this->getMainAlias($queryBuilder);
            }

            if (is_string($this->defaultOrderBy)) {
                $key = $alias . '.' . $this->defaultOrderBy;

                // One field
                $queryBuilder
                    ->addOrderBy($key);

            } elseif (is_array($this->defaultOrderBy)) {
                // Many fields (field name => sort suffix)
                foreach ($this->defaultOrderBy as $field => $asc) {
                    $key = $alias . '.' . $field;
                    $queryBuilder
                        ->addOrderBy($key, $asc ? 'ASC' : 'DESC');
                }

            }
            $queryBuilder->addOrderBy($alias . '.' . $this->getPrimaryKeyField(), 'ASC');
        }

        return $queryBuilder;
    }

    /**
     * Creates a new QueryBuilder instance that is prepopulated for this entity name.
     *
     * @param string $alias   Table alias OPTIONAL
     * @param string $indexBy The index for the from.
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    public function createQueryBuilder($alias = null, $indexBy = null)
    {
        if (null === $alias) {
            $alias = $this->getDefaultAlias();
        }

        $qb = $this->getQueryBuilder()
            ->select($alias)
            ->from($this->_entityName, $alias, $indexBy);

        return $this->assignDefaultOrderBy($qb, $alias);
    }

    /**
     * Create a new QueryBuilder instance that is prepopulated for this entity name
     * NOTE: without any relative subqueries!
     *
     * @param string $alias Table alias OPTIONAL
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createPureQueryBuilder($alias = null)
    {
        $alias = $alias ?: $this->getDefaultAlias();

        return $this->getQueryBuilder()
            ->select($alias)
            ->from($this->_entityName, $alias);
    }

    /**
     * Get default alias
     *
     * @return string
     */
    public function getDefaultAlias()
    {
        if (null === $this->defaultAlias) {
            $list = explode('\\', $this->_entityName);
            $this->defaultAlias = strtolower(substr(array_pop($list), 0, 1));
        }

        return $this->defaultAlias;
    }

    /**
     * Count records
     *
     * @return integer
     */
    public function count()
    {
        return $this->defineCountQuery()->count();
    }

    /**
     * Count records by conditions
     *
     * @param array $conditions Conditions
     *
     * @return integer
     */
    public function countBy(array $conditions = array())
    {
        return (int) $this->defineCountByQuery($conditions)->getSingleScalarResult();
    }

    /**
     * Wrapper
     *
     * @param mixed    $id          Entity identifier
     * @param int      $lockMode    The lock mode.
     * @param int|null $lockVersion The lock version.
     *
     * @return \XLite\Model\AEntity
     */
    public function find($id, $lockMode = null, $lockVersion = null)
    {
        if (is_array($id)) {
            foreach ($id as $k => $v) {
                if (!in_array($k, $this->_class->identifier, true)) {
                    unset($id[$k]);
                }
            }

            if (empty($id)) {
                $id = null;
            }
        }

        return null !== $id ? parent::find($id, $lockMode, $lockVersion) : null;
    }

    /**
     * Find entities by id's list
     *
     * @param array  $ids    Id's list
     * @param string $prefix Prefix OPTIONAL
     *
     * @return array
     */
    public function findByIds(array $ids, $prefix = 'arr')
    {
        if (1 < count($this->_class->identifier)) {
            // TODO - add throw exception
        }

        $result = array();

        if ($ids) {
            $qb = $this->createQueryBuilder();
            $alias = $this->getMainAlias($qb);
            $qb->andWhere($qb->expr()->in($alias . '.' . $this->_class->identifier[0], $ids));

            $result = $qb->getResult();
        }

        return $result;
    }

    /**
     * Find data frame
     *
     * @param integer $start Start offset OPTIONAL
     * @param integer $limit Frame length OPTIONAL
     *
     * @return array
     */
    public function findFrame($start = 0, $limit = 0)
    {
        return $this->defineFrameQuery($start, $limit)->getResult();
    }

    /**
     * Finds an entity by its primary key / identifier and return entity detached
     *
     * @param mixed $id The identifier.
     *
     * @return \XLite\Model\AEntity|void
     */
    public function findDetached($id)
    {
        $entity = parent::find($id);

        if ($entity) {
            $this->_em->detach($entity);
        }

        return $entity;
    }

    /**
     * Flushes all changes to objects that have been queued up to now to the database
     *
     * @param \XLite\Model\AEntity $entity Entity
     *
     * @return void
     */
    public function flushChanges($entity = null)
    {
        $this->getEntityManager()->flush($entity);
    }

    // {{{ Methods to manage entities

    /**
     * Insert entity
     *
     * @param \XLite\Model\AEntity|array $entity Entity to insert
     * @param boolean                    $flush  Flag OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function insert($entity = null, $flush = self::FLUSH_BY_DEFAULT)
    {
        $entity = $this->performInsert($entity);

        if ($flush) {
            $this->flushChanges();
        }

        return $entity;
    }

    /**
     * Insert entities
     *
     * @param array   $entities List of entities to insert
     * @param boolean $flush    Flag OPTIONAL
     *
     * @return array
     */
    public function insertInBatch(array $entities, $flush = self::FLUSH_BY_DEFAULT)
    {
        $result = array();

        foreach ($entities as $entity) {
            $result[] = $this->performInsert($entity);
        }

        if ($flush) {
            $this->flushChanges();
        }

        return $result;
    }

    /**
     * Update entity
     *
     * @param \XLite\Model\AEntity $entity Entity to update
     * @param array                $data   New values for entity properties
     * @param boolean              $flush  Flag OPTIONAL
     *
     * @return void
     */
    public function update(\XLite\Model\AEntity $entity, array $data = array(), $flush = self::FLUSH_BY_DEFAULT)
    {
        $this->performUpdate($entity, $data);

        if ($flush) {
            $this->flushChanges();
        }
    }

    /**
     * Find entity by ID and update it
     *
     * @param integer $id    ID of entity to update
     * @param array   $data  New values for entity properties
     * @param boolean $flush Flag OPTIONAL
     *
     * @return void
     */
    public function updateById($id, array $data = array(), $flush = self::FLUSH_BY_DEFAULT)
    {
        $this->update($this->getById($id), $data, $flush);
    }

    /**
     * Update entities
     *
     * @param array   $entities List of entities to update
     * @param boolean $flush    Flag OPTIONAL
     *
     * @return void
     */
    public function updateInBatch(array $entities, $flush = self::FLUSH_BY_DEFAULT)
    {
        foreach ($entities as $entity) {
            $this->performUpdate($entity);
        }

        if ($flush) {
            $this->flushChanges();
        }
    }

    /**
     * Find entities by ID and update them
     *
     * @param array   $data  Array of <id => array(properties)> elements
     * @param boolean $flush Flag OPTIONAL
     *
     * @return void
     */
    public function updateInBatchById(array $data, $flush = self::FLUSH_BY_DEFAULT)
    {
        foreach ($data as $id => $properties) {
            $this->performUpdate($this->getById($id), $properties);
        }

        if ($flush) {
            $this->flushChanges();
        }
    }

    /**
     * Delete entity
     *
     * @param \XLite\Model\AEntity $entity Entity to delete
     * @param boolean              $flush  Flag OPTIONAL
     *
     * @return void
     */
    public function delete(\XLite\Model\AEntity $entity, $flush = self::FLUSH_BY_DEFAULT)
    {
        $this->performDelete($entity);

        if ($flush) {
            $this->flushChanges();
        }
    }

    /**
     * Find entity by ID and delete it
     *
     * @param integer $id    ID of entity to delete
     * @param boolean $flush Flag OPTIONAL
     *
     * @return void
     */
    public function deleteById($id, $flush = self::FLUSH_BY_DEFAULT)
    {
        $this->delete($this->getById($id), $flush);
    }

    /**
     * Delete entities
     *
     * @param array   $entities List of entities to delete
     * @param boolean $flush    Flag OPTIONAL
     *
     * @return void
     */
    public function deleteInBatch(array $entities, $flush = self::FLUSH_BY_DEFAULT)
    {
        foreach ($entities as $entity) {
            $this->performDelete($entity);
        }

        if ($flush) {
            $this->flushChanges();
        }
    }

    /**
     * Find entities by ID and delete them
     *
     * @param array   $data  Array of <id => array(properties)> elements
     * @param boolean $flush Flag OPTIONAL
     *
     * @return void
     */
    public function deleteInBatchById(array $data, $flush = self::FLUSH_BY_DEFAULT)
    {
        foreach ($data as $id => $tmp) {
            $this->performDelete($this->getById($id));
        }

        if ($flush) {
            $this->flushChanges();
        }
    }

    /**
     * Insert single entity
     *
     * @param \XLite\Model\AEntity|array $entity Data to insert OPTIONAL
     *
     * @return \XLite\Model\AEntity|array
     */
    protected function performInsert($entity = null)
    {
        if (null === $entity || !($entity instanceof \XLite\Model\AEntity)) {
            $entity = new $this->_entityName((array) $entity);
        }

        // Since Doctrine lifecycle callbacks do not allow
        // to modify associations, we've added this method
        $entity->prepareEntityBeforeCommit($entity::ACTION_INSERT);

        $this->getEntityManager()->persist($entity);

        return $entity;
    }

    /**
     * Update single entity
     *
     * @param \XLite\Model\AEntity $entity Entity to use
     * @param array                $data   Data to save OPTIONAL
     *
     * @return void
     */
    protected function performUpdate(\XLite\Model\AEntity $entity, array $data = array())
    {
        $entity->map($data);

        // Since Doctrine lifecycle callbacks do not allow
        // to modify associations, we've added this method
        $entity->prepareEntityBeforeCommit($entity::ACTION_UPDATE);
    }

    /**
     * Delete single entity
     *
     * @param \XLite\Model\AEntity $entity Entity to detach
     *
     * @return void
     */
    protected function performDelete(\XLite\Model\AEntity $entity)
    {
        // Since Doctrine lifecycle callbacks do not allow
        // to modify associations, we've added this method
        $entity->prepareEntityBeforeCommit($entity::ACTION_DELETE);

        $this->getEntityManager()->remove($entity);
    }

    // }}}

    /**
     * Delete all records in associated table
     *
     * @return void
     */
    public function clearAll()
    {
        $this->deleteInBatch($this->findAll());
    }

    /**
     * Iterate all models
     *
     * @return \Iterator
     */
    public function iterateAll()
    {
        return $this->createPureQueryBuilder()->iterate();
    }
    
    /**
     * Define items iterator
     *
     * @param integer $position Position OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getRemoveDataIterator($position = 0)
    {
        return $this->defineRemoveDataQueryBuilder($position)
            ->setMaxResults(\XLite\Core\EventListener\RemoveData::CHUNK_LENGTH)
            ->iterate();
    }

    /**
     * Define remove data iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineRemoveDataQueryBuilder($position)
    {
        return $this->createPureQueryBuilder();
    }

    /**
     * Count items for remove data
     *
     * @return integer
     */
    public function countForRemoveData()
    {
        return (int) $this->defineCountForRemoveDataQuery()->getSingleScalarResult();
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForRemoveDataQuery()
    {
        $qb = $this->defineRemoveDataQueryBuilder(0)
            ->setMaxResults(1000000000);

        return $qb->select(
            'COUNT(DISTINCT ' . $qb->getMainAlias() . '.' . $this->getPrimaryKeyField() . ')'
        );
    }

    /**
     * Return name of the primary key field.
     * This method is used to determine entity persistence
     *
     * @return string
     */
    public function getPrimaryKeyField()
    {
        return $this->getClassMetadata()->getSingleIdentifierFieldName();
    }

    /**
     * Return info about model field
     *
     * @param string $field Field name
     * @param string $param Data param OPTIONAL
     *
     * @return array|mixed
     */
    public function getFieldInfo($field, $param = null)
    {
        try {
            $result = $this->getClassMetadata()->getFieldMapping($field);

        } catch (\Doctrine\ORM\Mapping\MappingException $exception) {
            $result = $this->getClassMetadata()->getAssociationMapping($field);
        }

        return \Includes\Utils\ArrayManager::getIndex($result, $param, null !== $param);
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
        $identifiers = $this->collectIdentifiersByRecord($data);

        return $identifiers ? $this->findOneBy($identifiers) : null;
    }

    /**
     * Load fixtures
     *
     * @param array                $data        Data
     * @param \XLite\Model\AEntity $parent      Entity parent callback OPTIONAL
     * @param array                $parentAssoc Entity mapped propery method OPTIONAL
     *
     * @return void
     */
    public function loadFixtures(array $data, \XLite\Model\AEntity $parent = null, array $parentAssoc = array())
    {
        list($regular, $assocs) = $this->getEntityProperties();
        foreach ($data as $record) {
            $this->loadFixture($record, $regular, $assocs, $parent, $parentAssoc);
        }
    }

    /**
     * Load fixture
     *
     * @param array                $record      Record
     * @param array                $regular     Regular fields info OPTIONAL
     * @param array                $assocs      Associations info OPTIONAL
     * @param \XLite\Model\AEntity $parent      Entity parent callback OPTIONAL
     * @param array                $parentAssoc Entity mapped propery method OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function loadFixture(
        array $record,
        array $regular = array(),
        array $assocs = array(),
        \XLite\Model\AEntity $parent = null,
        array $parentAssoc = array()
    ) {
        if (!$regular || !$assocs) {
            list($regular, $assocs) = $this->getEntityProperties();
        }

        // Strongly insert entity
        $insert = \XLite\Core\Database::getInstance()->getFixturesLoadingOption('insert');
        $entity = $insert ? null : $this->findOneByRecord($record, $parent);
        if (!$entity && !$insert && $parent && $parentAssoc && $parentAssoc['getter'] && !$parentAssoc['many']) {
            $entity = $parent->{$parentAssoc['getter']}();
        }

        // Add specified model directive
        $addModel = \XLite\Core\Database::getInstance()->getFixturesLoadingOption('addModel');
        $isAddModel = false;
        if ($addModel === $this->_class->name) {
            $isAddModel = true;

        } elseif ($addModel
            && !$entity
            && !\XLite\Core\Database::getInstance()->getFixturesLoadingOption('isAddModel')
            && !\XLite\Core\Database::getInstance()->getFixturesLoadingOption('addParent')
        ) {
            return null;
        }

        if (!$entity) {
            $class = $this->_class->name;
            $entity = new $class;
        }

        if ($parent) {
            $this->linkLoadedEntity($entity, $parent, $parentAssoc);
        }

        if ($isAddModel) {
            \XLite\Core\Database::getInstance()->setFixturesLoadingOption('isAddModel', true);
        }

        $this->loadRawFixture($entity, $record, $regular, $assocs);

        if ($isAddModel) {
            \XLite\Core\Database::getInstance()->setFixturesLoadingOption('isAddModel');
        }

        return $entity;
    }

    /**
     * Load raw fixture
     *
     * @param \XLite\Model\AEntity $entity  Entity
     * @param array                $record  Record
     * @param array                $regular Regular fields info OPTIONAL
     * @param array                $assocs  Associations info OPTIONAL
     *
     * @return void
     */
    public function loadRawFixture(\XLite\Model\AEntity $entity, array $record, array $regular = array(), array $assocs = array())
    {
        if (!$regular || !$assocs) {
            list($regular, $assocs) = $this->getEntityProperties();
        }

        if (!\XLite\Core\Database::getEM()->contains($entity)) {
            $entity->map($this->assembleRegularFieldsFromRecord($record, $regular));
            $this->insert($entity, false);

        } else {
            $this->update($entity, $this->assembleRegularFieldsFromRecord($record, $regular), false);
        }

        if ($this->flushAfterLoading) {
            \XLite\Core\Database::getEM()->flush();
        }

        foreach ($this->assembleAssociationsFromRecord($record, $assocs) as $name => $value) {
            if ($assocs[$name]['many']) {
                $assocs[$name]['repo']->loadFixtures($value, $entity, $assocs[$name]);

            } else {
                $assocs[$name]['repo']->loadFixture(
                    $value,
                    array(),
                    array(),
                    $entity,
                    $assocs[$name]
                );
            }
        }
    }

    /**
     * Get entity properties
     *
     * @return array(array)
     */
    public function getEntityProperties()
    {
        if (null === $this->entityProperties) {
            $cmd = $this->getClassMetadata();

            $regular = array();
            foreach ($cmd->fieldMappings as $f => $mapping) {
                $fCamelCase = \XLite\Core\Converter::convertToCamelCase($f);
                $regular[$f] = array(
                    'getter'    => 'get' . $fCamelCase,
                    'setter'    => 'set' . $fCamelCase,
                    'mapping'   =>  $mapping
                );
            }

            $identifiers = $cmd->identifier;

            $assocs = array();
            foreach ($cmd->associationMappings as $f => $fData) {
                $isMany = $fData['type'] === \Doctrine\ORM\Mapping\ClassMetadataInfo::ONE_TO_MANY
                    || $fData['type'] === \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY;

                $fCamelCase = \XLite\Core\Converter::convertToCamelCase($f);
                $targetEntity = $fData['targetEntity'];

                $assoc = array(
                    'many'         => $isMany,
                    'many2many'    => \Doctrine\ORM\Mapping\ClassMetadataInfo::MANY_TO_MANY === $fData['type'],
                    'getter'       => 'get' . $fCamelCase,
                    'setter'       => ($isMany ? 'add' : 'set') . $fCamelCase,
                    'identifiers'  => array(),
                    'entityName'   => $targetEntity,
                    'mappedGetter' => null,
                    'mappedSetter' => null,
                    'repo'         => \XLite\Core\Database::getRepo($targetEntity),
                );

                foreach (\XLite\Core\Database::getEM()->getClassMetadata($targetEntity)->identifier as $ident) {
                    $identCamelCase = \XLite\Core\Converter::convertToCamelCase($ident);
                    $assoc['identifiers'][$ident] = array(
                        'getter' => 'get' . $identCamelCase,
                        'setter' => 'set' . $identCamelCase,
                    );
                }

                if ($fData['mappedBy']) {
                    $mappedCamelCase = \XLite\Core\Converter::convertToCamelCase($fData['mappedBy']);
                    $assoc['mappedGetter'] = 'get' . $mappedCamelCase;

                    if ($assoc['many2many']) {
                        $assoc['mappedSetter'] = 'add' . $mappedCamelCase;

                    } else {
                        $assoc['mappedSetter'] = 'set' . $mappedCamelCase;
                    }
                }

                $assocs[$f] = $assoc;
            }

            $this->entityProperties = array($regular, $assocs, $identifiers);
        }

        return $this->entityProperties;
    }

    /**
     * Unload fixtures
     *
     * @param array                $data        Data
     * @param \XLite\Model\AEntity $parent      Entity parent callback OPTIONAL
     * @param array                $parentAssoc Entity mapped propery method OPTIONAL
     *
     * @return integer
     */
    public function unloadFixtures(array $data, \XLite\Model\AEntity $parent = null, array $parentAssoc = array())
    {
        $result = 0;

        list($regular, $assocs) = $this->getEntityProperties();
        foreach ($data as $record) {
            $result += $this->unloadFixture($record, $regular, $assocs, $parent, $parentAssoc);
        }

        return $result;
    }

    /**
     * Unload fixture
     *
     * @param array                $record      Record
     * @param array                $regular     Regular fields info OPTIONAL
     * @param array                $assocs      Associations info OPTIONAL
     * @param \XLite\Model\AEntity $parent      Entity parent callback OPTIONAL
     * @param array                $parentAssoc Entity mapped propery method OPTIONAL
     *
     * @return \XLite\Model\AEntity
     */
    public function unloadFixture(
        array $record,
        array $regular = array(),
        array $assocs = array(),
        \XLite\Model\AEntity $parent = null,
        array $parentAssoc = array()
    ) {

        $result = 0;

        if (!$regular || !$assocs) {
            list(, $assocs) = $this->getEntityProperties();
        }

        $entity = $this->findOneByRecord($record, $parent);

        if ($entity) {
            $addModel = \XLite\Core\Database::getInstance()->getFixturesLoadingOption('addModel');

            if (!$addModel || $addModel === $this->_class->name) {
                \XLite\Core\Database::getEM()->remove($entity);
                $result++;

            } elseif ($addModel) {
                // Search target model into children list
                foreach ($this->assembleAssociationsFromRecord($record, $assocs) as $name => $value) {
                    if ($assocs[$name]['many']) {
                        $result += $assocs[$name]['repo']->unloadFixtures($value, $entity, $assocs[$name]);

                    } else {
                        $result += $assocs[$name]['repo']->unloadFixture(
                            $value,
                            array(),
                            array(),
                            $entity,
                            $assocs[$name]
                        );
                    }
                }

            }
        }

        return $result;
    }

    /**
     * Check if it's possible to disable model DB table
     *
     * @return boolean
     */
    public function canDisableTable()
    {
        return true;
    }

    // {{{ Cache

    /**
     * Get cache driver
     *
     * @return \XLite\Core\Cache\Registry
     */
    public static function getCacheDriver()
    {
        if (null === static::$cacheDriver) {
            static::$cacheDriver = new \XLite\Core\Cache\Registry('datacache');
        }

        return static::$cacheDriver;
    }

    /**
     * Clean up all cache cells
     *
     * @return void
     */
    public function cleanCache()
    {
        array_map(array($this, 'deleteCache'), array_keys($this->defineCacheCells()));
    }

    /**
     * Get public class metadata
     *
     * @return array
     */
    public function getPublicClassMetadata()
    {
        return $this->getClassMetadata();
    }

    /**
     * Get model table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->_class->getTableName();
    }

    /**
     * Define cache cells
     *
     * @return array
     */
    protected function defineCacheCells()
    {
        return array();
    }

    /**
     * Get cache cells
     *
     * @param string $key Cell name OPTIONAL
     *
     * @return array
     */
    protected function getCacheCells($key = null)
    {
        if (null === $this->cacheCells) {
            $this->cacheCells = $this->restoreCacheCells();
        }

        return $key
            ? (isset($this->cacheCells[$key]) ? $this->cacheCells[$key] : null)
            : $this->cacheCells;
    }

    /**
     * Restore cache cells info from cache
     *
     * @return array
     */
    protected function restoreCacheCells()
    {
        $key = $this->getHashPrefix('cells');

        $cacheCells = \XLite\Model\Repo\ARepo::getCacheDriver()->fetch($key);

        if (!is_array($cacheCells)) {
            $cacheCells = $this->defineCacheCells();

            list($cacheCells, $relations) = $this->postprocessCacheCells($cacheCells);

            \XLite\Model\Repo\ARepo::getCacheDriver()->save($key, $cacheCells, self::CACHE_DEFAULT_TTL);

            // Save relations to current model cache cells from related models
            foreach ($relations as $model => $cells) {
                \XLite\Core\Database::getRepo($model)->addCacheRelations($cells);
            }
        }

        return $cacheCells;
    }

    /**
     * Postprocess cache cells info
     *
     * @param array $cacheCells Cache cells
     *
     * @return array (cache cells & relations data)
     */
    protected function postprocessCacheCells(array $cacheCells)
    {
        $relations = array();

        // Normalize cache cells
        foreach ($cacheCells as $name => $cell) {
            // Set default cell type
            if (!isset($cell[self::KEY_TYPE_CACHE_CELL])) {
                $cell[self::KEY_TYPE_CACHE_CELL] = self::DEFAULT_KEY_TYPE;
            }

            // Set default cell attributes list
            if (!isset($cell[self::ATTRS_CACHE_CELL])) {
                $cell[self::ATTRS_CACHE_CELL] = null;
            }

            // Set default cell relations list
            if (!isset($cell[self::RELATION_CACHE_CELL])) {
                $cell[self::RELATION_CACHE_CELL] = array();
            }

            // Collect related models
            foreach ($cell[self::RELATION_CACHE_CELL] as $model) {
                if (!isset($relations[$model])) {
                    $relations[$model] = array($this->_entityName => array($name));

                } elseif (!isset($relations[$model][$this->_entityName])) {
                    $relations[$model][$this->_entityName] = array($name);

                } else {
                    $relations[$model][$this->_entityName][] = $name;
                }
            }

            // Set cell attributes converter method name
            $method = $this->getCacheParamsConverterName($name);
            $cell[self::CONVERTER_CACHE_CELL] = method_exists($this, $method)
                ? $method
                : false;

            // Set cell hash generator method name
            if (self::CACHE_CUSTOM_KEY === $this->cacheCells[$name][self::KEY_TYPE_CACHE_CELL]) {
                $cell[self::GENERATOR_CACHE_CELL] = $this->getCacheHashGeneratorName($name);
            }

            $cacheCells[$name] = $cell;
        }

        return array($cacheCells, $relations);
    }

    /**
     * Get data from cache
     *
     * @param string $name   Cache cell name
     * @param array  $params Cache cell parameters OPTIONAL
     *
     * @return mixed|void
     */
    protected function getFromCache($name, array $params = array())
    {
        $result = null;
        $cell = $this->getCacheCells($name);

        if ($cell) {
            $result = \XLite\Model\Repo\ARepo::getCacheDriver()->fetch(
                $this->getCellHash($name, $cell, $params)
            );

        } else {
            // TODO - throw exception
        }

        return (null !== $result && false !== $result) ? $result : null;
    }

    /**
     * Save data to cache
     *
     * @param mixed  $data   Data
     * @param string $name   Cache cell name
     * @param array  $params Cache cell parameters OPTIONAL
     *
     * @return void
     */
    protected function saveToCache($data, $name, array $params = array())
    {
        $cell = $this->getCacheCells($name);

        if ($cell) {
            if ($data instanceof \ArrayAccess) {
                $this->detachList($data);

            } elseif ($data instanceof \XLite\Model\AEntity) {
                $data->detach();
            }

            \XLite\Model\Repo\ARepo::getCacheDriver()->save(
                $this->getCellHash($name, $cell, $params),
                $data,
                self::CACHE_DEFAULT_TTL
            );

        } else {
            // TODO - add throw exception
        }
    }

    /**
     * Get cell hash
     *
     * @param string $name   Cell name
     * @param array  $cell   Cell
     * @param array  $params Cache parameters
     *
     * @return string|void
     */
    protected function getCellHash($name, array $cell, array $params)
    {
        $hash = null;

        if (self::CACHE_ATTR_KEY === $cell[self::KEY_TYPE_CACHE_CELL]) {
            $hash = implode('.', $params);

        } elseif (self::CACHE_HASH_KEY === $cell[self::KEY_TYPE_CACHE_CELL]) {
            $hash = md5(implode('.', $params));

        } elseif (self::CACHE_CUSTOM_KEY === $cell[self::KEY_TYPE_CACHE_CELL]) {
            $hash = $this->{$cell[self::GENERATOR_CACHE_CELL]}($params);
        }

        if (null !== $hash && empty($hash)) {
            $hash = self::EMPTY_CACHE_CELL;
        }

        return $this->getHashPrefix() . '.' . $name . '.' . $hash;
    }

    /**
     * Get prefix for cache key
     *
     * @param string $suffix Cache subsection name OPTIONAL
     *
     * @return string
     */
    protected function getHashPrefix($suffix = 'data')
    {
        return str_replace('\\', '_', substr($this->_entityName, 6)) . '.' . $suffix;
    }

    /**
     * Get cell cache key generator method name
     *
     * @param string $name Cell name
     *
     * @return string
     */
    protected function getCacheHashGeneratorName($name)
    {
        return 'getCacheHash' . \XLite\Core\Converter::convertToCamelCase($name);
    }

    /**
     * Get cell cache parameters converter method name
     *
     * @param string $name Cell name
     *
     * @return string
     */
    protected function getCacheParamsConverterName($name)
    {
        return 'convertRecordToParams' . \XLite\Core\Converter::convertToCamelCase($name);
    }

    // }}}

    /**
     * Get Query builder main alias
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder
     *
     * @return string
     */
    protected function getMainAlias(\Doctrine\ORM\QueryBuilder $qb)
    {
        return $qb->getMainAlias();
    }

    /**
     * Get query builder
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function getQueryBuilder()
    {
        if (null === $this->queryBuilderClass) {
            $this->queryBuilderClass = str_replace('\\Repo\\', '\\QueryBuilder\\', get_called_class());

            if (!\XLite\Core\Operator::isClassExists($this->queryBuilderClass)) {
                $this->queryBuilderClass = '\XLite\Model\QueryBuilder\Base\Common';
            }
        }

        $class = $this->queryBuilderClass;

        return new $class($this->_em);
    }

    /**
     * Define query for count() method
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCountQuery()
    {
        return $this->createPureQueryBuilder();
    }

    /**
     * Define query for countBy() method
     *
     * @param array $conditions Conditions
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineCountByQuery(array $conditions)
    {
        $qb = $this->createPureQueryBuilder();
        $alias = $this->getMainAlias($qb);
        foreach ($conditions as $name => $value) {
            $qb->andWhere($alias . '.' . $name . ' = :' . $name)
                ->setParameter($name, $value);
        }

        return $qb->select('COUNT(' . implode(', ', $this->getIdentifiersList($qb)) . ')')
            ->setMaxResults(1);
    }

    /**
     * Define query for 'findFrame()' finder
     *
     * @param integer $start Start offset
     * @param integer $limit Frame length
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function defineFrameQuery($start, $limit)
    {
        return $this->createPureQueryBuilder()->setFrameResults($start, $limit);
    }

    /**
     * Get identifiers list for specified query builder object
     *
     * @param \Doctrine\ORM\QueryBuilder $qb Query builder
     *
     * @return array
     */
    protected function getIdentifiersList(\Doctrine\ORM\QueryBuilder $qb)
    {
        $alias = $this->getMainAlias($qb);

        $list = array();

        foreach ($this->_class->identifier as $i) {
            $list[] = $alias . '.' . $i;
        }

        return $list;
    }

    /**
     * Detach entities list
     *
     * @param mixed $data Entites list
     *
     * @return array
     */
    protected function detachList($data)
    {
        if (is_array($data) || $data instanceof \ArrayAccess) {
            foreach ($data as $item) {
                $item->detach();
            }
        }

        return $data;
    }

    /**
     * Search entity by key.
     * If it's not found, the exception will be thrown
     *
     * @param integer $id Entity ID
     *
     * @return \XLite\Model\AEntity
     * @throws \Exception
     */
    protected function getById($id)
    {
        $entity = $this->find($id);

        if (!$entity) {
            throw new \Exception(get_class($this) . '::getById() - unknown ID (' . $id . ')');
        }

        return $entity;
    }

    /**
     * getAllowedModifiers
     *
     * @return array
     */
    protected function getAllowedModifiers()
    {
        return array('insert', 'update', 'delete');
    }

    /**
     * Pattern to check called method names
     *
     * @return string
     */
    protected function getModifierPattern()
    {
        return '/(' . implode('|', $this->getAllowedModifiers()) . ')(InBatch)?(ById)?/Si';
    }

    /**
     * Collect identifiers array by record
     *
     * @param array $data Record
     *
     * @return array(mixed)|boolean
     */
    protected function collectIdentifiersByRecord(array $data)
    {
        $identifiers = array();
        $found = false;

        list(, , $classIdentifiers) = $this->getEntityProperties();
        if ($classIdentifiers) {
            $found = true;
            foreach ($classIdentifiers as $ident) {
                if (!isset($data[$ident])) {
                    $found = false;
                    break;
                }

                $identifiers[$ident] = $data[$ident];
            }
        }

        if (!$found) {
            $identifiers = $this->collectAlternativeIdentifiersByRecord($data);
            if ($identifiers) {
                $found = true;
            }
        }

        return $found ? $identifiers : false;
    }

    /**
     * Collect alternative identifiers by record
     *
     * @param array $data Record
     *
     * @return boolean|array(mixed)
     */
    protected function collectAlternativeIdentifiersByRecord(array $data)
    {
        $found = false;
        $identifiers = array();

        if ($this->alternativeIdentifier) {
            // Collect identifiers by alternative unqiue keys
            foreach ($this->alternativeIdentifier as $keys) {
                foreach ($keys as $key) {
                    $found = true;
                    if (!isset($data[$key])) {
                        $found = false;
                        break;
                    }

                    $identifiers[$key] = $data[$key];
                }

                if ($found) {
                    break;
                }
            }
        }

        return $found ? $identifiers : false;
    }

    /**
     * Assemble regular fields from record
     *
     * @param array $record  Record
     * @param array $regular Regular fields info OPTIONAL
     *
     * @return array
     */
    protected function assembleRegularFieldsFromRecord(array $record, array $regular = array())
    {
        if (!$regular) {
            list($regular, ) = $this->getEntityProperties();
        }

        return array_intersect_key($record, $regular);
    }

    /**
     * Assemble associations from record
     *
     * @param array $record Record
     * @param array $assocs Associations info OPTIONAL
     *
     * @return array
     */
    protected function assembleAssociationsFromRecord(array $record, array $assocs = array())
    {
        if (!$assocs) {
            list(, $assocs) = $this->getEntityProperties();
        }

        $record = array_intersect_key($record, $assocs);
        foreach ($record as $name => $value) {
            if (!is_array($value)) {
                unset($record[$name]);
            }
        }

        return $record;

    }

    /**
     * Link loaded entity to parent object
     *
     * @param \XLite\Model\AEntity $entity      Loaded entity
     * @param \XLite\Model\AEntity $parent      Entity parent callback
     * @param array                $parentAssoc Entity mapped propery method
     *
     * @return void
     */
    protected function linkLoadedEntity(\XLite\Model\AEntity $entity, \XLite\Model\AEntity $parent, array $parentAssoc)
    {
        if (!$parentAssoc['many']
            || !$entity->getUniqueIdentifier()
            || !$parent->{$parentAssoc['getter']}()->contains($entity)
        ) {
            // Add entity to parent
            $parent->{$parentAssoc['setter']}($entity);

            // Add parent to entity
            if ($parentAssoc['mappedSetter']) {
                $entity->{$parentAssoc['mappedSetter']}($parent);
            }
        }
    }

    /**
     * Get calculated field dql
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $name         Field name
     *
     * @return string
     */
    protected function getCalculatedField(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $name)
    {
        $uname = ucfirst($name);
        $method = 'defineCalculated' . $uname . 'DQL';

        if (method_exists($this, $method)
            && !$queryBuilder->getDataCell(static::CALCULATED_FIELD_FLAG . $name)
        ) {
            $queryBuilder->setDataStorage(
                static::CALCULATED_FIELD_FLAG . $name,
                $this->$method($queryBuilder, $queryBuilder->getRootAlias())
            );
        }

        return $queryBuilder->getDataCell(static::CALCULATED_FIELD_FLAG . $name);
    }

    /**
     * Get calculated field alias (to use in ORDER BY clause as example)
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $name         Field name
     *
     * @return string
     */
    protected function getCalculatedFieldAlias(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $name)
    {
        if (
            !$queryBuilder->getDataCell(static::CALCULATED_FIELD_ALIAS_FLAG . $name)
            && $dql = $this->getCalculatedField($queryBuilder, $name)
        ) {
            $field = 'calculatedField' . ucfirst($name);

            $queryBuilder->addSelect("({$dql}) as {$field}");

            $queryBuilder->setDataStorage(
                static::CALCULATED_FIELD_ALIAS_FLAG . $name,
                $field
            );
        }

        return $queryBuilder->getDataCell(static::CALCULATED_FIELD_ALIAS_FLAG . $name);
    }

    /**
     * Assign calculated field
     * @deprecated 5.3.3 use getCalculatedFieldAlias
     *
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder Query builder
     * @param string                                  $name         Field name
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function assignCalculatedField(\XLite\Model\QueryBuilder\AQueryBuilder $queryBuilder, $name)
    {
        $uname = ucfirst($name);
        $method = 'defineCalculated' . $uname . 'DQL';
        if (method_exists($this, $method)
            && null === $queryBuilder->getDataCell(static::CALCULATED_FIELD_FLAG . $name)
        ) {
            $value = $this->$method($queryBuilder, $queryBuilder->getRootAlias());
            $queryBuilder->addSelect($value . ' calculated' . $uname);
            $queryBuilder->setDataStorage(static::CALCULATED_FIELD_FLAG . $name, $value);
        }

        return $queryBuilder;
    }

    /**
     * Helper to get a valid value for {sort, order} pair.
     * It returns  array($sort, null)   value if $value = array($sort)
     * and returns array($sort, $order) value if $value = array($sort, $order)
     *
     * @param array $value
     *
     * @return array
     */
    protected function getSortOrderValue($value)
    {
        $order = 'ASC';
        $sort = null;

        if (count($value) === 1) {
            list($sort) = $value;
        } elseif (count($value) === 2) {
            list($sort, $order) = $value;
        }

        if (!$sort && $this->defaultOrderBy) {
            $sort = $this->getDefaultAlias() . '.' . $this->defaultOrderBy;
        }

        return array($sort, $order);
    }

    // {{{ Export routines

    /**
     * Set selection of item ids for export
     *
     * @param array $filter Identificators
     */
    public function setExportSelection(array $selection)
    {
        $this->exportSelection = $selection;
    }

    /**
     * Set selection of item ids for export
     *
     * @param \XLite\Core\CommonCell $filter Identificators
     */
    public function setExportFilter($filter)
    {
        if (!empty($filter)) {
            $this->clearExportFilter();
            $this->hasFilter = true;

            $restrictedConditions = $this->getExportRestrictedCondition();

            foreach ($restrictedConditions as $cndName) {
                if (isset($filter->{$cndName})) {
                    unset($filter->{$cndName});
                }
            }

            $ids = $this->search($filter, static::SEARCH_MODE_IDS);

            \Includes\Utils\ArrayManager::eachCons(
                $ids,
                1000,
                array($this, 'writeExportIds')
            );
        }
    }

    /**
     * Write ids to xcPendingExport field
     *
     * @param array $ids Array of exported ids
     */
    public function writeExportIds(array $ids)
    {
        $expr = new \Doctrine\ORM\Query\Expr();
        $alias = $this->getDefaultAlias();

        $updateQb = $this->getQueryBuilder()
            ->update($this->_entityName, $alias)
            ->set($alias . '.xcPendingExport', 1)
            ->where(
                $expr->in(
                    $alias . '.' . $this->getPrimaryKeyField(),
                    ':ids'
                )
            )
            ->setParameter('ids', $ids);

        $updateQb->execute();
    }

    /**
     * Checks if condition key is restricted to use
     *
     * @return array
     */
    public function getExportRestrictedCondition()
    {
        return array(
            'limit',
            'sortBy',
            'orderBy',
        );
    }

    /**
     * Set selection of item ids for export
     *
     * @param array $filter Identificators
     */
    public function clearExportFilter()
    {
        $alias = $this->getDefaultAlias();
        $this->getQueryBuilder()
             ->update($this->_entityName, $alias)
             ->set($alias . '.xcPendingExport', 0)
             ->execute();
    }

    /**
     * Count items for export routine
     *
     * @return integer
     */
    public function countForExport()
    {
        return (int) $this->defineCountForExportQuery()
                          ->getSingleScalarResult();
    }

    /**
     * Define items iterator
     *
     * @param integer $position
     * @param int     $maxItems
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    public function getExportIterator($position = 0, $maxItems = 100)
    {
        return $this->defineExportIteratorQueryBuilder($position)
            ->setMaxResults($maxItems)
            ->iterate();
    }

    /**
     * Define query builder for COUNT query
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineCountForExportQuery()
    {
        $qb = $this->createPureQueryBuilder()
                   ->select(
                       'COUNT(DISTINCT ' . $this->getDefaultAlias() . '.' . $this->getPrimaryKeyField() . ')'
                   );

        if ($this->hasFilter) {
            $qb->andWhere($this->getFilterExpr($qb));
        }

        if (!empty($this->exportSelection)) {
            $qb->andWhere($this->getSelectionExpr($qb))
                ->setParameter('ids', $this->exportSelection);
        }

        return $qb;
    }

    /**
     * Define export iterator query builder
     *
     * @param integer $position Position
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineExportIteratorQueryBuilder($position)
    {
        $qb = $this->createPureQueryBuilder()
                   ->setFirstResult($position)
                   ->setMaxResults(1000000000);

        if ($this->hasFilter) {
            $qb->andWhere($this->getFilterExpr($qb));
        }

        if (!empty($this->exportSelection)) {
            $qb->andWhere($this->getSelectionExpr($qb))
                 ->setParameter('ids', $this->exportSelection);
        }

        return $qb;
    }

    /**
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb
     *
     * @return \Doctrine\ORM\Query\Expr\Base
     */
    protected function getFilterExpr(\XLite\Model\QueryBuilder\AQueryBuilder $qb)
    {
        return $qb->expr()->eq(
            $qb->getMainAlias() . '.xcPendingExport',
            true
        );
    }

    /**
     * @param \XLite\Model\QueryBuilder\AQueryBuilder $qb
     *
     * @return \Doctrine\ORM\Query\Expr\Base
     */
    protected function getSelectionExpr(\XLite\Model\QueryBuilder\AQueryBuilder $qb)
    {
        return $qb->getMainAlias() . '.' . $this->getPrimaryKeyField() .' IN (:ids)';
    }

    // }}}

    // {{{ Import

    /**
     * Find model by import conditions
     *
     * @param array $conditions Conditions
     *
     * @return \XLite\Model\AEntity
     */
    public function findOneByImportConditions(array $conditions)
    {
        return $this->defineFindOneByImportConditionsQueryBuilder($conditions)->getSingleResult();
    }

    /**
     * Define import query builder
     *
     * @param array $conditions Conditions
     *
     * @return \XLite\Model\QueryBuilder\AQueryBuilder
     */
    protected function defineFindOneByImportConditionsQueryBuilder(array $conditions)
    {
        $qb = $this->createQueryBuilder();
        foreach ($conditions as $name => $value) {
            $this->addImportCondition($qb, $name, $value);
        }

        return $qb;
    }

    /**
     * Get identifiers list for specified query builder object
     *
     * @param \Doctrine\ORM\QueryBuilder $qb    Query builder
     * @param string                     $name  Name
     * @param mixed                      $value Value
     *
     * @return void
     */
    protected function addImportCondition(\Doctrine\ORM\QueryBuilder $qb, $name, $value)
    {
        $added = false;

        $alias = $qb->getMainAlias();
        if (property_exists($this->getClassMetadata()->name, $name)) {
            $qb->andWhere($alias . '.' . $name . ' = :' . $name);
            $added = true;

        } elseif (property_exists($this->getClassMetadata()->name, 'translations')) {
            $qb->andWhere('translations.' . $name . ' = :'. $name);
            if (is_array($value)) {
                $value = $value[\XLite\Logic\Import\Importer::getLanguageCode()];
            }
            $added = true;
        }

        if ($added) {
            $qb->setParameter($name, $value);
        }
    }

    // }}}

    // {{{ Search

    /**
     * Check if condition excluded
     *
     * @param string $conditionName Condition name
     *
     * @return boolean
     */
    protected function isConditionExcluded($conditionName)
    {
        $excludedConditions = $this->getExcludedConditions();

        $excludeCondition = isset($excludedConditions[$conditionName])
            ? $excludedConditions[$conditionName]
            : false;

        $isCurrentSearchModeExcluded = is_array($excludeCondition)
            && in_array($this->searchState['searchMode'], $excludeCondition);

        return $excludeCondition === static::EXCLUDE_FROM_ANY || $isCurrentSearchModeExcluded;
    }

    /**
     * Excluded search conditions
     *
     * Returns an array
     * Keys are excluded search condition names
     * Values are arrays of excluded search modes for that condition or service value EXCLUDE_FROM_ANY
     * If value is EXCLUDE_FROM_ANY condition won't be evaluated, but still will be available in other conditions
     *
     * @return array
     */
    protected function getExcludedConditions()
    {
        return array(
            static::P_LIMIT => array(
                static::SEARCH_MODE_COUNT
            )
        );
    }

    /**
     * Get allowed, not excluded
     *
     * @return array
     */
    protected function getAllowedConditions()
    {
        $conditions = array();

        foreach ($this->searchState['currentSearchCnd'] as $name => $condition) {
            if (!$this->isConditionExcluded($name)) {
                $conditions[$name]  = $condition;
            }
        }

        return $conditions;
    }

    /**
     * Prepare conditions for search
     *
     * @return void
     */
    protected function processConditions()
    {
        $conditions = $this->getAllowedConditions();

        // First process expression conditions
        // They should be processes first because of $qb->add without append nature
        foreach ($conditions as $name => $condition) {
            if ($condition instanceof \XLite\Model\SearchCondition\IExpressionProvider) {
                $this->processExpressionSearchCondition($condition);
                unset($conditions[$name]);
            }
        }
        $this->fillQueryBuilderFromExpressions();

        // Process old conditions after
        foreach ($conditions as $name => $condition) {
            $this->processRepositoryHandlerSearchCondition($condition, $name);
        }
    }

    /**
     * Fill queryBuilder from expression
     *
     * @return void
     */
    protected function fillQueryBuilderFromExpressions()
    {
        foreach ($this->searchState['parts'] as $partType => $expression) {
            if ($partType === 'where') {
                foreach ($expression as $key => $value) {
                    $this->searchState['queryBuilder']->andWhere($value);
                }
            } else {
                $this->searchState['queryBuilder']->add($partType, $expression);
            }
        }

        foreach ($this->searchState['parameters'] as $name => $value) {
            $this->searchState['queryBuilder']->setParameter($name, $value);
        }

        foreach ($this->searchState['joins'] as $name => $value) {
            $this->searchState['queryBuilder']->linkLeft($value->getJoin(), $value->getAlias());
        }
    }

    /**
     * Process old conditions for search
     *
     * @param \XLite\Core\CommonCell $conditions Conditions to process
     *
     * @return void
     */
    protected function processRepositoryHandlerSearchCondition($condition, $name)
    {
        if($condition instanceof \XLite\Model\SearchCondition\IRepositoryHandlerCarrier) {
            $name       = $condition->getHandlerName();
            $condition  = $condition->getValue();
        }

        $this->callSearchConditionHandler($condition, $name);
    }

    /**
     * Process ExpressionSearchCondition
     *
     * @param \XLite\Model\SearchCondition\IExpressionProvider     $condition  Condition
     *
     * @return void
     */
    protected function processExpressionSearchCondition(\XLite\Model\SearchCondition\IExpressionProvider $condition)
    {
        $alias = $this->getMainAlias($this->searchState['queryBuilder']);

        $cndType = $condition->getExpressionType();
        if (!isset($this->searchState['parts'][$cndType])) {
            $this->searchState['parts'][$cndType] = array();
        }
        $this->searchState['parts'][$cndType][] = $condition->getExpression($alias);

        $this->searchState['parameters'] += $condition->getParameters();
        $this->searchState['joins'] = array_merge(
            $this->searchState['joins'],
            $condition->getJoins($alias)
        );
    }

    /**
     * Call corresponded method to handle a search condition
     *
     * @param mixed     $value      Condition data
     * @param string    $key        Condition name
     *
     * @return void
     */
    protected function callSearchConditionHandler($value, $key)
    {
        $methodName = 'prepareCnd' . \Includes\Utils\Converter::convertToCamelCase($key);

        if (method_exists($this, $methodName)) {
            // N.B. Backwards compatibility of countOnly and queryBuilder
            $countOnly = $this->searchState['searchMode'] === static::SEARCH_MODE_COUNT;
            $this->$methodName($this->searchState['queryBuilder'], $value, $countOnly);

        } else {
            $message = sprintf(
                "Trying to use an undefined search condition:\n | Repo: %s\n | Condition: %s\n | search() called from: %s",
                get_class($this),
                $key,
                $this->getSearchCaller()
            );
            \XLite\Logger::getInstance()->log($message, LOG_DEBUG);
        }
    }

    protected function getSearchCaller()
    {
        $callerClass = 'unknown';

        if (intval(\Includes\Utils\ConfigParser::getOptions(array('log_details', 'level'))) !== LOG_DEBUG) {
            return $callerClass;
        }

        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS | DEBUG_BACKTRACE_PROVIDE_OBJECT, 10);

        $lastCallBeforeSearch = null;
        foreach ($backtrace as $key => $line) {
            if (isset($line['function'])
                && $line['function'] === 'search'
                && isset($backtrace[$key + 1])
            ) {
                $lastCallBeforeSearch = $backtrace[$key + 1];
                break;
            }
        }

        if ($lastCallBeforeSearch && isset($lastCallBeforeSearch['object'])) {
            $callerClass = get_class($lastCallBeforeSearch['object']);
            if (isset($lastCallBeforeSearch['function'])) {
                $callerClass .= '#' . $lastCallBeforeSearch['function'];
            }
        }

        return $callerClass;
    }

    /**
     * Common search
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function processQueryBuilder()
    {
        $this->searchState['queryBuilder'] = $this->getQueryBuilderForSearch();

        $this->processConditions();

        return $this->searchState['queryBuilder'];
    }

    /**
     * Common search
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQueryBuilderForSearch()
    {
        return $this->createQueryBuilder();
    }

    /**
     * Get search modes handlers
     *
     * @return array
     */
    protected function getSearchModes()
    {
        return array(
            static::SEARCH_MODE_COUNT     => 'searchCount',
            static::SEARCH_MODE_ENTITIES  => 'searchResult',
            static::SEARCH_MODE_INDEXED     => 'searchIndexed',
            static::SEARCH_MODE_IDS       => 'searchIds',
        );
    }

    /**
     * Get search mode handler
     *
     * @param string $mode Search mode
     *
     * @return callable
     */
    protected function getSearchMode($mode)
    {
        $modes = $this->getSearchModes();

        return array($this, $modes[$mode]);
    }

    /**
    * Check if search in countOnly mode
    *
     * @return boolean
     */
    protected function isCountSearchMode()
    {
        return isset($this->searchState['searchMode'])
            && $this->searchState['searchMode'] === static::SEARCH_MODE_COUNT;
    }

    /**
     * Common search
     *
     * @param \XLite\Core\CommonCell $cnd           Search conditions                   OPTIONAL
     * @param boolean|string         $searchMode    Return items list or only its size  OPTIONAL
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    public function search(\XLite\Core\CommonCell $cnd = null, $searchMode = self::SEARCH_MODE_ENTITIES)
    {
        if (!$cnd) {
            $cnd = new \XLite\Core\CommonCell();
        }

        // N.B. Backwards compatibility, was $countOnly boolean instead of $searchMode 'enum'
        if (is_bool($searchMode)) {
            $searchMode = $searchMode === false
                ? static::SEARCH_MODE_ENTITIES
                : static::SEARCH_MODE_COUNT;
        }

        $this->clearSearchState();

        $this->searchState['currentSearchCnd']  = $cnd;
        $this->searchState['searchMode']        = $searchMode;
        $this->searchState['queryBuilder']      = $this->processQueryBuilder();

        $result = null;

        $searchHandler = $this->getSearchMode($searchMode);
        $result = call_user_func($searchHandler);

        return $result;
    }

    /**
     * Search count only routine.
     *
     * @return integer
     */
    protected function searchCount()
    {
        $queryBuilder = $this->postprocessSearchCountQueryBuilder($this->searchState['queryBuilder']);

        return (int)($queryBuilder->getSingleScalarResult());
    }

    /**
     * Search result routine.
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function searchResult()
    {
        $queryBuilder = $this->postprocessSearchResultQueryBuilder($this->searchState['queryBuilder']);

        return $queryBuilder->getOnlyEntities();
    }

    /**
     * Search result routine.
     *
     * @return \Doctrine\ORM\PersistentCollection
     */
    protected function searchIndexed()
    {
        $queryBuilder = $this->postprocessSearchIndexedQueryBuilder($this->searchState['queryBuilder']);

        return $queryBuilder->getObjectResult();
    }

    /**
     * Prepare queryBuilder for searchCount() method
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function postprocessSearchCountQueryBuilder($queryBuilder)
    {
        $key = $this->getSearchPrimaryFields($queryBuilder);

        $queryBuilder->select('COUNT(DISTINCT ' . $key . ')')
            ->resetDQLPart('groupBy')
            ->resetDQLPart('orderBy');

        return $queryBuilder;
    }

    /**
     * Prepare queryBuilder for searchResult() method
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function postprocessSearchResultQueryBuilder($queryBuilder)
    {
        $key = $this->getSearchPrimaryFields($queryBuilder);

        $queryBuilder->groupBy($key);

        return $queryBuilder;
    }

    /**
     * Prepare queryBuilder for searchResult() method
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function postprocessSearchIndexedQueryBuilder($queryBuilder)
    {
        $key = $this->getSearchPrimaryFields($queryBuilder);

        $queryBuilder->indexBy($this->getMainAlias($queryBuilder), $this->getMainAlias($queryBuilder) . '.' . $this->getPrimaryKeyField());
        $queryBuilder->groupBy($key);

        return $queryBuilder;
    }

    /**
     * Get primary fields list for searchResult() and searchCount() methods
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder
     *
     * @return string
     */
    protected function getSearchPrimaryFields($queryBuilder)
    {
        return $this->getMainAlias($queryBuilder) . '.' . $this->getPrimaryKeyField();
    }

    /**
     * Search result ids routine.
     *
     * @return \Doctrine\ORM\PersistentCollection|integer
     */
    protected function searchIds()
    {
        $queryBuilder = $this->searchState['queryBuilder'];

        $alias = $this->getDefaultAlias();

        $result = $queryBuilder
            ->select($alias . '.' . $this->getPrimaryKeyField())
            ->getQuery()->getScalarResult();

        return array_map(
            function ($item) {
                return $item[$this->getPrimaryKeyField()];
            },
            $result
        );
    }

    // }}}

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndLimit(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        $this->searchState['queryBuilder']->setFrameResults($value);
    }

    /**
     * Prepare certain search condition
     *
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder Query builder to prepare
     * @param array                      $value        Condition data
     *
     * @return void
     */
    protected function prepareCndOrderBy(\Doctrine\ORM\QueryBuilder $queryBuilder, array $value)
    {
        if (!$value
            || $this->searchState['searchMode'] === static::SEARCH_MODE_COUNT
        ) {
            return;
        }

        $value = is_array($value[0]) ? $value : array($value);

        $orderByCnds = [];
        foreach ($value as $orderByCnd) {
            list($sort, $order) = $this->getSortOrderValue($orderByCnd);

            if (!$sort || !$order) {
                continue;
            }

            $orderByCnds[] = [$sort, $order];
        }

        if ($orderByCnds) {
            $this->searchState['queryBuilder']
                ->resetDQLPart('orderBy');

            foreach ($orderByCnds as $orderByCnd) {
                list($sort, $order) = $orderByCnd;
                $this->searchState['queryBuilder']
                    ->addOrderBy($sort, $order);
            }
        }

        $this->assignDefaultOrderBy($this->searchState['queryBuilder']);
    }

    /**
     * Get a UUID that represents current state of all entities of this type.
     *
     * Same version implies same entities state.
     *
     * @return string
     */
    public function getVersion()
    {
        $evRepo = \XLite\Core\Database::getRepo('XLite\Model\EntityTypeVersion');

        return $evRepo->getEntityTypeVersion($this->getClassName());
    }

    /**
     * Generates and stores a new version UUID for this entity type.
     *
     * Entity type versions are automatically updated upon EntityManager#flush. However, it is your responsibility to call bumpVersion() manually if you update entities using DQL or native queries.
     */
    public function bumpVersion()
    {
        $evRepo = \XLite\Core\Database::getRepo('XLite\Model\EntityTypeVersion');

        $evRepo->bumpEntityTypeVersion($this->getClassName());
    }
}
