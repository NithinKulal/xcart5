<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Plugin\Doctrine\Utils;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Includes\Autoloader;
use Includes\Decorator\Plugin\Doctrine\Utils\ModelGenerator\AnnotationReader;

/**
 * EntityManager
 *
 */
abstract class EntityManager extends \Includes\Decorator\Plugin\Doctrine\ADoctrine
{
    /**
     * Entity manager
     *
     * @var \Doctrine\ORM\EntityManager
     */
    protected static $handler;

    /**
     * Model classes metadata
     *
     * @var array
     */
    protected static $metadata;

    /**
     * Return all classes metadata
     *
     * @param string $class Class name OPTIONAL
     *
     * @return array
     */
    public static function getAllMetadata($class = null)
    {
        if (!isset(static::$metadata)) {
            static::$metadata = array();

            // Create hash array to quick access its elements
            foreach (static::getHandler()->getMetadataFactory()->getAllMetadata() as $data) {
                static::$metadata[$data->name] = $data;
            }
        }

        return \Includes\Utils\ArrayManager::getIndex(static::$metadata, $class);
    }

    /**
     * Generate models
     *
     * @return void
     */
    public static function generateModels()
    {
        $config = static::getConfig();

        AnnotationRegistry::registerFile(LC_DIR_CLASSES . 'XLite/Core/Doctrine/Annotation/Behavior.php');
        AnnotationRegistry::registerFile(LC_DIR_CLASSES . 'XLite/Core/Doctrine/Annotation/Purpose.php');

        Autoloader::switchToOriginalClassDir();

        $reader = new AnnotationReader();
        $reader->addNamespace('Doctrine\ORM\Mapping');
        $cachedReader = new CachedReader($reader, new ArrayCache());

        $paths = array_merge(
            [LC_DIR_CLASSES . 'XLite/Model'],
            glob(LC_DIR_CLASSES . 'XLite/Module/*/*/Model/') ?: []
        );

        $metadataDriver = new AnnotationDriver($cachedReader, $paths);

        $config->setMetadataDriverImpl(
            $metadataDriver
        );

        $config->setClassMetadataFactoryName('Includes\Decorator\Plugin\Doctrine\Utils\ModelGenerator\ClassMetadataFactory');

        $em = \Doctrine\ORM\EntityManager::create(static::getDSN(), $config);

        $metadataFactory = $em->getMetadataFactory();

        $metas = $metadataFactory->getAllMetadata();

        static::getEntityGenerator()->generate(
            $metas,
            LC_DIR_CLASSES
        );
    }

    /**
     * Generate proxies
     *
     * @return void
     */
    public static function generateProxies()
    {
        static::getHandler()->getProxyFactory()->generateProxyClasses(
            static::getAllMetadata(),
            \Includes\Decorator\ADecorator::getCacheModelProxiesDir()
        );
    }

    /**
     * Retur DSN as params array
     *
     * @return array
     */
    protected static function getDSN()
    {
        return \Includes\Utils\Database::getConnectionParams(true) + array('driver' => 'pdo_mysql');
    }

    /**
     * Set metadata driver for Doctrine config
     *
     * @param \Doctrine\ORM\Configuration $config Config object
     *
     * @return void
     */
    protected static function setMetadataDriver(\Doctrine\ORM\Configuration $config)
    {
        $root = static::getClassesDir();

        $paths = array_merge(
            [$root . 'XLite/Model'],
            glob($root . 'XLite/Module/*/*/Model/') ?: []
        );

        $config->setMetadataDriverImpl(
            $config->newDefaultAnnotationDriver($paths)
        );
    }

    /**
     * Return the Doctrine config object
     *
     * @return \Doctrine\ORM\Configuration
     */
    protected static function getConfig()
    {
        $config = new \XLite\Core\Doctrine\ORM\Configuration();
        $config->setAutoGenerateProxyClasses(false);

        static::setMetadataDriver($config);

        // Set proxy settings
        $config->setProxyDir(rtrim(\Includes\Decorator\ADecorator::getCacheModelProxiesDir(), LC_DS));
        $config->setProxyNamespace(LC_MODEL_PROXY_NS);

        $cache = new \Doctrine\Common\Cache\ArrayCache();
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);

        return $config;
    }

    /**
     * Return instance of the entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected static function getHandler()
    {
        if (!isset(static::$handler)) {
            static::$handler = \Doctrine\ORM\EntityManager::create(static::getDSN(), static::getConfig());
            \XLite\Core\Database::registerCustomTypes(static::$handler);
        }

        return static::$handler;
    }

    /**
     * Return the Doctrine tools
     *
     * @return \Doctrine\ORM\Tools\EntityGenerator
     */
    protected static function getEntityGenerator()
    {
        $generator = new \Includes\Decorator\Plugin\Doctrine\Utils\ModelGenerator();
        $generator->setGenerateAnnotations(true);
        $generator->setRegenerateEntityIfExists(false);
        $generator->setUpdateEntityIfExists(true);
        $generator->setGenerateStubMethods(true);
        $generator->setNumSpaces(4);
        $generator->setClassToExtend('\XLite\Model\AEntity');
        $generator->setBackupExisting(false);

        return $generator;
    }
}
