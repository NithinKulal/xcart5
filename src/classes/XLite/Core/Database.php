<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

use Includes\Database\Migration\Migration;
use Includes\Database\Migration\MigrationFactory;
use Includes\Decorator\Plugin\Doctrine\Utils\SchemaMigrationManager;
use XLite\Core\Database\Migration\PersistenceStrategy;
use XLite\Core\Database\Migration\ReadStrategy;
use XLite\Core\Doctrine\ORM\Repository\RepositoryFactory;
use Includes\Decorator\ADecorator;
use XLite\Core\Model\EntityVersion\EntityVersionUpdater;


/**
 * Database
 */
class Database extends \XLite\Base\Singleton
{
    /**
     * Schema generation modes
     */
    const SCHEMA_CREATE = 'create';
    const SCHEMA_UPDATE = 'update';
    const SCHEMA_DELETE = 'delete';

    /**
     * DB schema file indent
     */
    const SCHEMA_FILE_INDENT = '  ';

    /**
     * Charset which is used for DB connection
     */
    const DB_CONNECTION_CHARSET = 'utf8';

    /**
     * Scheme name pattern
     */
    const SCHEMA_NAME_PATTERN = '`?([^\s`\),]+)`?';

    /**
     * Doctrine entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected static $em;

    /**
     * Doctrine cache driver
     *
     * @var \Doctrine\Common\Cache\CacheProvider
     */
    protected static $cacheDriver;

    /**
     * Cache drivers query
     *
     * @var array
     */
    protected static $cacheDriversQuery = array(
        'apc',
        'xcache',
        'memcache',
    );

    /**
     * Doctrine config object
     *
     * @var \Doctrine\ORM\Configuration
     */
    protected $configuration;

    /**
     * Table prefix
     *
     * @var string
     */
    protected $tablePrefix = '';

    /**
     * connected
     *
     * @var boolean
     */
    protected $connected;

    /**
     * Doctrine unmanaged table names list
     *
     * @var array
     */
    protected $unmanagedTables = array();

    /**
     * Forbid truncate tables if will truncate store-based tables
     *
     * @var array
     */
    protected $forbidTruncateTablesStore = array(
        'profiles',
        'currencies',
        'payment_methods',
        'shipping_methods',
        'memberships',
    );

    /**
     * Fixtures loading procedure options
     *
     * @var array
     */
    protected $fixturesLoadingOptions = array(
        'insert'    => false,
        'addModel'  => null,
        'addParent' => true,
    );

    /**
     * Get entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public static function getEM()
    {
        // FIXME: add __constructStatic
        if (null === static::$em) {
            \XLite\Core\Database::getInstance();
        }

        return static::$em;
    }

    /**
     * Get repository (short method)
     *
     * @param string $repository Entity class name
     *
     * @return \XLite\Model\Repo\ARepo
     */
    public static function getRepo($repository)
    {
        $repository = static::getEntityClass($repository);

        return static::loadAnnotationClass($repository)
            ? static::getEM()->getRepository($repository)
            : null;
    }

    /**
     * Calculate the class name for entity
     *
     * @param string $entity Entity
     *
     * @return string
     */
    public static function getEntityClass($entity)
    {
        return ltrim($entity, '\\');
    }

    /**
     * Register custom types
     *
     * @param \Doctrine\ORM\EntityManager $em Entity manager
     *
     * @return void
     */
    public static function registerCustomTypes(\Doctrine\ORM\EntityManager $em)
    {
        // Money
        if (!\Doctrine\DBAL\Types\Type::hasType('money')) {
            \Doctrine\DBAL\Types\Type::addType('money', 'XLite\Core\ColumnType\Money');
        }

        // Decimal
        if (\Doctrine\DBAL\Types\Type::hasType('decimal')) {
            \Doctrine\DBAL\Types\Type::overrideType('decimal', 'XLite\Core\ColumnType\Decimal');
        }

        // Register annotation class loader
        \Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(
            array(get_called_class(), 'loadAnnotationClass')
        );
    }

    /**
     * Load annotation class
     *
     * @param string $class Short class name
     *
     * @return boolean
     */
    public static function loadAnnotationClass($class)
    {
        return \XLite\Core\Operator::isClassExists($class) && class_exists($class);
    }


    /**
     * Get cache driver
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public static function getCacheDriver()
    {
        return static::$cacheDriver;
    }

    /**
     * Get cache driver
     *
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public static function getFreshCacheDriver()
    {
        return new \XLite\Core\Cache();
    }

    /**
     * Get last query length
     *
     * @return integer
     */
    public static function getLastQueryLength()
    {
        $length = static::getEM()
            ->getConnection()
            ->executeQuery('SELECT FOUND_ROWS()', array())
            ->fetchColumn();

        return (int) $length;
    }

    /**
     * Prepare array for IN () DQL function
     *
     * @param array  $data   Hash array
     * @param string $prefix Placeholder prefix OPTIONAL
     *
     * @return array (keys for IN () function & parameters hash array)
     */
    public static function prepareArray(array $data, $prefix = 'arr')
    {
        $keys = array();
        $parameters = array();

        foreach ($data as $k => $v) {
            $k = $prefix . $k;
            $keys[] = ':' . $k;
            $parameters[$k] = $v;
        }

        return array($keys, $parameters);
    }

    /**
     * @deprecated 5.3.3
     *
     * Build IN () condition
     *
     * @param \Doctrine\ORM\QueryBuilder $qb     Query builder
     * @param array                      $data   Hash array
     * @param string                     $prefix Placeholder prefix OPTIONAL
     *
     * @return array Keys for IN () function
     */
    public static function buildInCondition(\Doctrine\ORM\QueryBuilder $qb, array $data, $prefix = 'arr')
    {
        list($keys, $data) = static::prepareArray($data, $prefix);

        foreach ($data as $k => $v) {
            $qb->setParameter($k, $v);
        }

        return $keys;
    }


    /**
     * Detect APC cache driver
     *
     * @return boolean
     */
    protected static function detectCacheDriverApc()
    {
        return function_exists('apc_cache_info');
    }

    /**
     * Detect XCache cache driver
     *
     * @return boolean
     */
    protected static function detectCacheDriverXcache()
    {
        return function_exists('xcache_get');
    }

    /**
     * Detect Memcache cache driver
     *
     * @return boolean
     */
    protected static function detectCacheDriverMemcache()
    {
        return function_exists('memcache_connect');
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        if (!$this->connected) {
            $this->connect();
        }
    }

    /**
     * Connect and set-up Doctrine
     *
     * @return void
     */
    public function connect()
    {
        $this->configuration = new \XLite\Core\Doctrine\ORM\Configuration();

        // Setup cache
        $this->setDoctrineCache();

        // Setup metadata driver
        $paths = array_merge(
            [ADecorator::getCacheModelsDir()],
            glob(ADecorator::getCacheClassesDir() . 'XLite/Module/*/*/Model/')
        );

        $excludePaths = [
            ADecorator::getCacheModelProxiesDir()
        ];

        $excludePaths = array_filter($excludePaths, 'file_exists');

        $annotationDriver = $this->configuration->newDefaultAnnotationDriver($paths);

        $annotationDriver->addExcludePaths($excludePaths);

        $this->configuration->setMetadataDriverImpl($annotationDriver);

        // Set proxy settings
        $this->configuration->setProxyDir(rtrim(ADecorator::getCacheModelProxiesDir(), LC_DS));
        $this->configuration->setProxyNamespace(LC_MODEL_PROXY_NS);

        $this->configuration->setAutoGenerateProxyClasses(\Doctrine\Common\Proxy\AbstractProxyFactory::AUTOGENERATE_NEVER);

        // Register custom functions
        $this->configuration->addCustomStringFunction('if', '\\XLite\\Core\\Doctrine\\IfFunction');
        $this->configuration->addCustomStringFunction('IFNULL', '\\XLite\\Core\\Doctrine\\IfnullFunction');
        $this->configuration->addCustomStringFunction('relevance', '\\XLite\\Core\\Doctrine\\RelevanceFunction');
        $this->configuration->addCustomNumericFunction('intval', '\\XLite\\Core\\Doctrine\\IntvalFunction');
        $this->configuration->addCustomStringFunction('findInSet', '\\XLite\\Core\\Doctrine\\FindInSetFunction');
        $this->configuration->addCustomStringFunction('castChar', '\\XLite\\Core\\Doctrine\\CastCharFunction');
        $this->configuration->addCustomStringFunction('collate', '\\XLite\\Core\\Doctrine\\CollateFunction');
        $this->configuration->addCustomStringFunction('least', '\\XLite\\Core\\Doctrine\\LeastFunction');
        $this->configuration->addCustomStringFunction('greatest', '\\XLite\\Core\\Doctrine\\GreatestFunction');
        $this->configuration->addCustomStringFunction('concat_ws', '\\XLite\\Core\\Doctrine\\ConcatWSFunction');

        $this->tablePrefix = trim(\XLite::getInstance()->getOptions(array('database_details', 'table_prefix')));

        $this->configuration->setRepositoryFactory(new RepositoryFactory());

        $this->configuration->setClassMetadataFactoryName('XLite\Core\Doctrine\ORM\Mapping\ClassMetadataFactory');

        // Add Doctrine SQL filter that controls which DB read operations are permitted when DB migration is in progress
        if (SchemaMigrationManager::getMigration() !== null && !defined('LC_CACHE_BUILDING')) {
            $readStrategy = new ReadStrategy(SchemaMigrationManager::getMigration());

            $readStrategy->registerSQLFilter($this->configuration);
        }

        // Initialize DB connection and entity manager
        $this->startEntityManager();

        // Enable SQL filter registered above
        if (isset($readStrategy)) {
            $readStrategy->enableSQLFilter(self::$em);
        }
    }

    /**
     * Start Doctrine entity manager
     *
     * @return void
     */
    public function startEntityManager()
    {
        // Initialize DB connection and entity manager
        static::$em = \Doctrine\ORM\EntityManager::create($this->getDSN(), $this->configuration);

        static::registerCustomTypes(static::$em);

        // Bind events
        $events = array(\Doctrine\ORM\Events::loadClassMetadata);
        if (static::$cacheDriver) {
            // Bind cache checkers
            $events[] = \Doctrine\ORM\Events::postPersist;
            $events[] = \Doctrine\ORM\Events::postUpdate;
            $events[] = \Doctrine\ORM\Events::postRemove;
        }

        $eventManager = static::$em->getEventManager();

        $eventManager->addEventListener($events, $this);

        $eventManager->addEventSubscriber($this->getRepo('XLite\Model\EntityTypeVersion'));
        
        $eventManager->addEventSubscriber(new EntityVersionUpdater());

        // Apply persistence strategy when not in cache rebuild mode
        if (SchemaMigrationManager::getMigration() !== null && !defined('LC_CACHE_BUILDING')) {
            $this->applyMigrationAwarePersistenceStrategy(SchemaMigrationManager::getMigration());
        }
    }

    /**
     * Apply persistence constraints when database schema migration is in progress.
     *
     * @param Migration $migration
     */
    protected function applyMigrationAwarePersistenceStrategy(Migration $migration)
    {
        $eventManager = static::$em->getEventManager();

        $persistenceStrategy = new PersistenceStrategy($migration);

        $eventManager->addEventSubscriber($persistenceStrategy);
    }

    // {{{ Export SQL to file

    /**
     * Export SQL dump from database to the specified file
     *
     * @param string  $path    File path or directory where SQL dump should be exported OPTIONAL
     * @param boolean $verbose Is export should be verbose flag OPTIONAL
     *
     * @return boolean
     */
    public function exportSQLToFile($path = null, $verbose = false)
    {
        $result = false;

        // Suppose that $path is var directory if it's null
        if (!$path) {
            $path = LC_DIR_VAR;
        }

        // Prepare file path
        $schemaFileName = is_dir($path)
            ? $path . LC_DS . 'schema.sql'
            : $path;

        try {
            $dsn = $this->getDSN();
            $dump = new \Rah\Danpu\Dump;
            $dump->file($schemaFileName)
                ->dsn($dsn['path'])
                ->user($dsn['user'])
                ->pass($dsn['password'])
                ->prefix($this->getTablePrefix())
                ->disableForeignKeyChecks(true)
                ->tmp(sys_get_temp_dir());

            try {
                $dump->events(false);
            } catch (\Exception $e) {
                // Danpu do not have this config option yet
            }

            new \XLite\Core\Rah\Danpu\Export($dump);

            $result = true;

        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Returns database schema as an array ('create_tables' => array(...), 'alter_table' => array(...))
     *
     * @param string $path Export directory path OPTIONAL
     *
     * @return array
     */
    public function getExportDBSchema($path = null)
    {
        $result = array();

        // Get array of SQL queries which are described of DB schema
        $schema = $this->createMigration(self::SCHEMA_CREATE)->getQueries();

        // Separate schema to the different parts: drop-create and alter-table queries
        foreach ($schema as $row) {
            if (preg_match('/^ALTER TABLE .+ ADD (CONSTRAINT|FOREIGN KEY)/', $row)) {
                $result['alter_table'][] = $row;

            } else {
                $result['create_table'][] = $row;
            }
        }

        return $result;
    }

    /**
     * Returns array of database data
     *
     * @return array
     * @throws
     */
    public function getExportDBData()
    {
        $result = array();

        // Get X-Cart table names
        $tableNames = static::$em->getConnection()->getSchemaManager()->listTableNames();

        // Get connection to the database
        $dbConnection = static::$em->getConnection();

        // Begin transaction to avoid data inconsistency
        $dbConnection->beginTransaction();

        try {
            foreach ($tableNames as $tableName) {
                // Get full data from each table
                $statement = $dbConnection->query('SELECT * FROM ' . $tableName);
                $rows = $statement->fetchAll(\PDO::FETCH_NUM);
                $statement->closeCursor();
                $statement = null;

                $insertValues = array();

                // Prepare compact INSERT statements for data
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $insertValues[] = '(' . implode(',', array_map(array($this, 'doQuote'), $row)) . ')';
                    }

                    $result[] = 'INSERT INTO ' . $tableName . ' VALUES ' . implode(',', $insertValues);
                }
            }

        } catch (\PDOException $e) {
            // Throws an exception if something wrong with transaction
            throw new \Exception($e);
        }

        $dbConnection->commit();

        return $result;
    }

    /**
     * Quote data gathered from database for writing to the file
     *
     * @param mixed $value Data of any type gathered from database
     *
     * @return string
     */
    public function doQuote($value)
    {
        // Array for replacements
        $search  = array("\x00", "\x0a", "\x0d", "\x1a");
        $replace = array('\0', '\n', '\r', '\Z');

        if (null === $value) {
            // Null must be presented as 'NULL' string
            $result = 'NULL';

        } elseif (is_string($value)) {
            // Do quoting string value
            $result = '\'' . str_replace($search, $replace, addslashes($value)) . '\'';

        } else {
            // Numeric values should not be quoted
            $result = $value;
        }

        return $result;
    }

    // }}}

    /**
     * Check - DB is empty or not
     *
     * @return boolean
     */
    public function isDBEmpty()
    {
        return 0 === count(static::$em->getConnection()->getSchemaManager()->listTableNames());
    }

    /**
     * Get DB schema mode
     *
     * @return string
     */
    public function getDBSchemaMode()
    {
        return $this->isDBEmpty() ? static::SCHEMA_CREATE : static::SCHEMA_UPDATE;
    }

    /**
     * Create / update DB schema
     *
     * @return integer
     */
    public function updateDBSchema()
    {
        return $this->executeQueries(
            $this->createMigration($this->isDBEmpty() ? static::SCHEMA_CREATE : static::SCHEMA_UPDATE)->getQueries()
        );
    }

    /**
     * Drop DB schema
     *
     * @return integer
     */
    public function dropDBSchema()
    {
        return $this->executeQueries($this->createMigration(static::SCHEMA_DELETE)->getQueries());
    }

    /**
     * Execute queries list
     *
     * @param array $queries Queries list
     *
     * @return integer
     */
    public function executeQueries(array $queries)
    {
        $i = 0;
        $connection = static::$em->getConnection();
        foreach ($queries as $sql) {
            $connection->executeQuery($sql);
            $i++;
        }

        return $i;
    }

    /**
     * Get DB schema as file
     *
     * @param string $mode Schema generation mode OPTIONAL
     *
     * @return string
     */
    public function getDBSchemaAsFile($mode = self::SCHEMA_CREATE)
    {
        return implode(';' . PHP_EOL, $this->createMigration($mode)->getQueries()) . ';' . PHP_EOL;
    }

    /**
     * Get Migration object which contains SQL queries that need to be performed and MigrationType object that describes migration safety (in some cases concurrent reads and even writes are permitted while the migration is in progress, MigrationType describes precisely which operations are permitted).
     *
     * @param $mode
     *
     * @return Migration
     */
    public function createMigration($mode)
    {
        $migrationFactory = new MigrationFactory(self::getEM(), $this);

        return $migrationFactory->createMigration($mode);
    }

    /**
     * Get fixtures loading procedure option
     *
     * @param string $name Option name
     *
     * @return mixed
     */
    public function getFixturesLoadingOption($name)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->fixturesLoadingOptions, $name, true);
    }

    /**
     * Set fixtures loading procedure option
     *
     * @param string $name  Option name
     * @param mixed  $value Option value OPTIONAL
     *
     * @return void
     */
    public function setFixturesLoadingOption($name, $value = null)
    {
        $this->fixturesLoadingOptions[$name] = $value;
    }

    /**
     * Load fixtures from YAML file
     *
     * @param string    $path       YAML file path
     * @param array     $options    Options         OPTIONAL
     *
     * @return void
     */
    public function loadFixturesFromYaml($path, $options = null)
    {
        $options = array_merge_recursive(
            $options ?: array(),
            [
                'allowedModels'  => null,
                'excludedModels' => null,
            ]
        );
        $data = \Symfony\Component\Yaml\Yaml::parse($path);

        if (is_array($data)) {
            foreach ($data as $entityName => $rows) {
                if (($options['allowedModels'] && !in_array($entityName, $options['allowedModels']))
                    || ($options['excludedModels'] && in_array($entityName, $options['excludedModels']))
                ) {
                    continue;
                }

                $repo = static::getRepo($entityName);

                if ($repo) {
                    $rows = $this->detectDirectives($rows);

                    \XLite\Core\Database::getInstance()->setFixturesLoadingOption(
                        'moduleName',
                        \Includes\Utils\ModulesManager::getFileModule($path)
                    );

                    $repo->loadFixtures($rows);

                    static::$em->flush();
                    static::$em->clear();

                    $this->resetDirectives();
                }
            }
        }
    }

    /**
     * Unload fixtures from YAML file
     *
     * @param string $path YAML file path
     *
     * @return boolean|integer
     */
    public function unloadFixturesFromYaml($path)
    {
        $data = \Symfony\Component\Yaml\Yaml::parse($path);

        $result = false;

        if (is_array($data)) {
            $result = 0;
            foreach ($data as $entityName => $rows) {
                $repo = static::getRepo($entityName);

                if ($repo) {
                    $rows = $this->detectDirectives($rows);

                    $result += $repo->unloadFixtures($rows);

                    static::$em->flush();
                    static::$em->clear();

                    $this->resetDirectives();
                }
            }
        }

        return $result;
    }

    /**
     * Get table prefix
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    /**
     * postPersist event handler
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $arg Event argument
     *
     * @return void
     */
    public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $arg)
    {
        $entity = $arg->getEntity();

        if ($entity instanceof \XLite\Model\AEntity) {
            $entity->checkCache();
        }
    }

    /**
     * postUpdate event handler
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $arg Event argument
     *
     * @return void
     */
    public function postUpdate(\Doctrine\ORM\Event\LifecycleEventArgs $arg)
    {
        $entity = $arg->getEntity();

        if ($entity instanceof \XLite\Model\AEntity) {
            $entity->checkCache();
        }
    }

    /**
     * postRemove event handler
     *
     * @param \Doctrine\ORM\Event\LifecycleEventArgs $arg Event argument
     *
     * @return void
     */
    public function postRemove(\Doctrine\ORM\Event\LifecycleEventArgs $arg)
    {
        $entity = $arg->getEntity();

        if ($entity instanceof \XLite\Model\AEntity) {
            $entity->checkCache();
        }
    }

    /**
     * loadClassMetadata event handler
     *
     * @param \Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs Event arguments
     *
     * @return void
     */
    public function loadClassMetadata(\Doctrine\ORM\Event\LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();
        $tableName = isset($classMetadata->table['originalName'])
            ? $classMetadata->table['originalName']
            : $classMetadata->getTableName();

        if ($classMetadata->discriminatorMap && $classMetadata->parentClasses) {
            $parentClassName = array_shift($classMetadata->parentClasses);
            $parentClassMetadata = static::getEM()->getMetadataFactory()->getMetadataFor($parentClassName);

            $tableName = isset($parentClassMetadata->table['originalName'])
                ? $parentClassMetadata->table['originalName']
                : $parentClassMetadata->getTableName();
        }

        // Set table name prefix
        $prefix = $this->getTablePrefix();
        if ($prefix && !$classMetadata->isMappedSuperclass) {
            if (($prefix . $tableName) !== $classMetadata->getTableName()) {
                $classMetadata->setTableName($prefix . $tableName);
                $classMetadata->table['originalName'] = $tableName;

                foreach ($classMetadata->associationMappings as &$mapping) {
                    if (isset($mapping['joinTable']) && $mapping['joinTable']) {
                        if (strpos($mapping['joinTable']['name'], $prefix) !== 0) {
                            $mapping['joinTable']['name'] = $prefix . $mapping['joinTable']['name'];
                        }
                    }
                }
            }
        }
    }

    /**
     * Get structures which must not be removed (do not remove tables)
     *
     * @return array
     */
    public function getEnabledStructuresToStore()
    {
        return $this->getRegistryStructures(true);
    }

    /**
     * Get structures which must not be removed (do not remove tables and foreign keys)
     *
     * @return array
     */
    public function getDisabledStructuresToStore()
    {
        return $this->getRegistryStructures(false);
    }

    /**
     * Get Registry structures
     *
     * @param bool $enabled Flag: if true then get enabled modules structure otherwise disabled
     *
     * @return array
     */
    public function getRegistryStructures($enabled)
    {
        $tables = array();
        $columns = array();

        $path = $enabled
            ? $this->getEnabledStructuresPath()
            : $this->getDisabledStructuresPath();

        if (file_exists($path)) {

            $dependencies = array();

            $modules = \XLite\Core\Operator::getInstance()->loadServiceYAML($path);

            if (is_array($modules)) {

                foreach ($modules as $module => $list) {
                    // Prepare dependencies list
                    if (!empty($list['dependencies']) && is_array($list['dependencies'])) {
                        $dependencies = array_merge_recursive($dependencies, $list['dependencies']);
                    }
                }

                foreach ($modules as $module => $list) {
                    if ($this->checkModuleState($module, $enabled)) {
                        if (isset($list['tables']) && is_array($list['tables'])) {
                            $tables = array_merge($tables, $list['tables']);
                        }

                        if (isset($list['columns']) && is_array($list['columns'])) {
                            $columns = \Includes\Utils\ArrayManager::mergeRecursiveDistinct($columns, $list['columns']);
                        }

                        if (isset($dependencies[$module])) {
                            $columns = \Includes\Utils\ArrayManager::mergeRecursiveDistinct($columns, $dependencies[$module]);
                        }

                    } elseif (!$enabled) {
                        \Includes\Utils\ModulesManager::removeModuleFromDisabledStructure($module);
                    }
                }
            }
        }

        return array($tables, $columns);
    }

    /**
     * Return true if module state equals the specified state
     *
     * @param string  $module Module Author\Name
     * @param boolean $state  Module state to check
     *
     * @return boolean
     */
    protected function checkModuleState($module, $state)
    {
        $result = true;

        $moduleState = static::getRepo('XLite\Model\Module')->getModuleState($module);

        if (null !== $moduleState) {
            $result = ($state == $moduleState);
        }

        return $result;
    }

    /**
     * Set disabled tables list
     *
     * @param string $module     Module unique name
     * @param array  $structures Disabled structures OPTIONAL
     *
     * @return void
     */
    public function setDisabledStructures($module, array $structures = array())
    {
        $remove = !$structures || (!$structures['tables'] && !$structures['columns']);

        if ($remove) {
            \Includes\Utils\ModulesManager::removeModuleFromDisabledStructure($module);

        } else {
            if (!\Includes\Utils\ModulesManager::moveModuleToDisabledRegistry($module)) {
                $path = $this->getDisabledStructuresPath();

                $data = file_exists($path)
                    ? \XLite\Core\Operator::getInstance()->loadServiceYAML($path)
                    : array();

                  $data[$module] = $structures;

                \Includes\Utils\ModulesManager::storeModuleRegistry($path, $data);
            }
        }
    }

    /**
     * Register Module in the ENABLED module registry
     *
     * @param string $module Module unique name
     * @param array  $data   Registry info structures OPTIONAL
     *
     * @return void
     */
    public function registerModuleToEnabledRegistry($module, array $data = array())
    {
        \Includes\Utils\ModulesManager::registerModuleToEnabledRegistry($module, $data);
    }

    /**
     * Clean entities cache
     *
     * @return void
     */
    public function cleanEntitiesCache()
    {
        foreach (static::getEM()->getMetadataFactory()->getAllMetadata() as $metadata) {
            if (!$metadata->isMappedSuperclass) {
                static::getRepo($metadata->name)->cleanCache();
            }
        }
    }

    /**
     * Import SQL
     *
     * @param string $sql SQL
     *
     * @return integer Lines count
     */
    public function importSQL($sql)
    {
        $lines = 0;

        $conn = static::$em->getConnection();

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        do {
            // Required due to "MySQL has gone away!" issue
            $stmt->fetch();
            $stmt->closeCursor();

            $lines++;
        } while ($stmt->nextRowset());

        return $lines;
    }

    /**
     * Import SQL from file
     *
     * @param string  $path    File path
     * @param boolean $verbose Is import should be verbose flag OPTIONAL
     *
     * @return integer Lines count
     * @throws
     */
    public function importSQLFromFile($path, $verbose = false)
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(
                sprintf('SQL file \'%s\' does not exist.', $path)
            );

        } elseif (!is_readable($path)) {
            throw new \InvalidArgumentException(
                sprintf('SQL file \'%s\' does not have read permissions.', $path)
            );
        }

        return $this->importSQL(file_get_contents($path));
    }

    /**
     * Truncate data by repository type
     *
     * @param string $type Repository type
     *
     * @return integer
     */
    public function truncateByType($type)
    {
        return $this->truncate($this->getTruncateTableNames($type));
    }

    /**
     * Truncate all data
     *
     * @param array $tableNames Table names OPTIONAL
     *
     * @return integer
     */
    public function truncate(array $tableNames = array())
    {
        if (!$tableNames) {
            $tableNames = $this->detectTruncateTables($this->getAllMetadata());
        }

        $sql = array();
        foreach ($tableNames as $tableName) {
            $sql[] = static::$em->getConnection()->getDatabasePlatform()->getTruncateTableSQL($tableName);
        }

        return $this->executeQueries($sql);
    }

    /**
     * Get all metadata
     *
     * @return array
     */
    protected function getAllMetadata()
    {
        $entities = array();
        foreach (static::$em->getMetadataFactory()->getAllMetadata() as $md) {
            if (!$md->isMappedSuperclass) {
                $entities[] = $md;
            }
        }

        return $entities;
    }

    /**
     * Detect fixtures loading directives
     *
     * @param array $rows Entity fixtures
     *
     * @return array
     */
    protected function detectDirectives(array $rows)
    {
        if (isset($rows['directives'])) {
            $this->fixturesLoadingOptions['insert'] = !empty($rows['directives']['insert']);

            if (isset($rows['directives']['addModel'])) {
                $this->fixturesLoadingOptions['addModel'] = $rows['directives']['addModel'];
            }

            if (isset($rows['directives']['addParent'])) {
                $this->fixturesLoadingOptions['addParent'] = (bool) $rows['directives']['addParent'];
            }

            unset($rows['directives']);
        }

        return $rows;
    }

    /**
     * Reset fixtures loading directives
     *
     * @return void
     */
    protected function resetDirectives()
    {
        $this->fixturesLoadingOptions['insert']    = false;
        $this->fixturesLoadingOptions['addModel']  = null;
        $this->fixturesLoadingOptions['addParent'] = true;
    }

    /**
     * Setup doctrine cache
     *
     * @return void
     */
    protected function setDoctrineCache()
    {
        static::$cacheDriver = new \XLite\Core\Cache();

        $driver = static::$cacheDriver->getDriver();
        $this->configuration->setMetadataCacheImpl($driver);
        $this->configuration->setQueryCacheImpl($driver);
        $this->configuration->setResultCacheImpl($driver);
    }

    /**
     * Get DSN in Doctrine style
     *
     * @return array
     */
    protected function getDSN()
    {
        $options = \XLite::getInstance()->getOptions('database_details');

        $dsnFields = array(
            'host'        => 'hostspec',
            'port'        => 'port',
            'unix_socket' => 'socket',
            'dbname'      => 'database',
        );
        $dsnList = array(
            'driver'       => 'pdo_mysql',
            'wrapperClass' => '\XLite\Core\Connection',
        );
        $dsnString = array();

        foreach ($dsnFields as $pdoOption => $lcOption) {
            if (!empty($options[$lcOption])) {
                $dsnList[$pdoOption] = $options[$lcOption];
                $dsnString[] = $pdoOption . '=' . $options[$lcOption];
            }
        }

        $dsnList['path'] = 'mysql:' . implode(';', $dsnString);
        $dsnList['user'] = $options['username'];
        $dsnList['password'] = $options['password'];
        $dsnList['charset'] = static::DB_CONNECTION_CHARSET;

        if ('pdo_mysql' === $dsnList['driver']) {
            $dsnList['driverClass'] = '\XLite\Core\PDOMySqlDriver';
            $dsnList['driverOptions'] = array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . static::DB_CONNECTION_CHARSET . ", sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))"
            );
        }

        return $dsnList;
    }

    /**
     * Get disabled tables list storage path
     *
     * @return string
     */
    protected function getDisabledStructuresPath()
    {
        return \Includes\Utils\ModulesManager::getDisabledStructuresPath();
    }

    /**
     * Get enabled tables list storage path
     *
     * @return string
     */
    protected function getEnabledStructuresPath()
    {
        return \Includes\Utils\ModulesManager::getEnabledStructurePath();
    }

    /**
     * Get table names by type for truncate
     *
     * @param string $type Repository type
     *
     * @return array
     */
    protected function getTruncateTableNames($type)
    {
        $list = $this->detectTruncateTables($this->getTruncateMetadatas($type));

        if (\XLite\Model\Repo\ARepo::TYPE_STORE === $type) {
            $forbid = array();
            foreach ($this->forbidTruncateTablesStore as $n) {
                $forbid[] = $this->getTablePrefix() . $n;
            }

            $list = array_diff($list, $forbid);
        }

        return $list;
    }

    /**
     * Get class metadata by type for truncate
     *
     * @param string $type Repository type
     *
     * @return array
     */
    protected function getTruncateMetadatas($type)
    {
        $list = array();

        foreach ($this->getAllMetadata() as $cmd) {
            if (static::getRepo($cmd->name)->getRepoType() === $type) {
                $list[] = $cmd;
            }
        }

        return $list;
    }

    /**
     * Detect truncate table names by class metadatas
     *
     * @param array $metadatas Class metadata list
     *
     * @return array
     */
    protected function detectTruncateTables(array $metadatas)
    {
        $calc = new \Doctrine\ORM\Internal\CommitOrderCalculator;

        foreach ($metadatas as $class) {
            $calc->addClass($class);

            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide']) {
                    $targetClass = static::getEM()->getMetadataFactory()->getMetadataFor($assoc['targetEntity']);

                    if (!$calc->hasClass($targetClass->name)) {
                        $calc->addClass($targetClass);
                    }

                    // add dependency ($targetClass before $class)
                    $calc->addDependency($targetClass, $class);
                }
            }
        }

        $commitOrder = $calc->getCommitOrder();

        $associationTables = array();

        foreach ($commitOrder as $class) {
            foreach ($class->associationMappings as $assoc) {
                if ($assoc['isOwningSide'] && $assoc['type'] === \Doctrine\ORM\Mapping\ClassMetadata::MANY_TO_MANY) {
                    $associationTables[] = $assoc['joinTable']['name'];
                }
            }
        }

        $orderedTables = array_unique($associationTables);

        // Truncate tables in reverse commit order
        foreach (array_reverse($commitOrder) as $class) {
            if (!$class->isMappedSuperclass
                && (!$class->isInheritanceTypeSingleTable() || $class->name === $class->rootEntityName)
                && !in_array($class->getTableName(), $orderedTables)
            ) {
                $orderedTables[] = $class->getTableName();
            }
        }

        return $orderedTables;
    }

    // {{{ Service methods

    /**
     * Load entity by its proxy entity. Use this method if you need to check if proxy entity is realy exists in the database
     *
     * @param \XLite\Model\AEntity $proxy Proxy model
     *
     * @return \XLite\Model\AEntity|null
     */
    public static function loadEntity($proxy)
    {
        $result = null;

        if ($proxy) {
            $className = get_class($proxy);
            $classMetadata   = \XLite\Core\Database::getEM()->getClassMetadata($className);
            $entityPersister = \XLite\Core\Database::getEM()->getUnitOfWork()->getEntityPersister($className);

            $result = $entityPersister->load($classMetadata->getIdentifierValues($proxy));
        }

        return $result;
    }

    // }}}
}
