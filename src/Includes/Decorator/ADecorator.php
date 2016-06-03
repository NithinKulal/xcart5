<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator;

/**
 * ADecorator
 *
 */
abstract class ADecorator
{
    /**
     * Cache building steps
     */
    const STEP_FIRST    = 1;
    const STEP_SECOND   = 2;
    const STEP_THIRD    = 3;
    const STEP_FOURTH   = 4;
    const STEP_FIFTH    = 5;
    const STEP_SIX      = 6;
    const STEP_SEVEN    = 7;
    const STEP_EIGHT    = 8;
    const STEP_NINE     = 9;
    const STEP_TEN      = 10;
    const STEP_ELEVEN   = 11;
    const STEP_TWELVE   = 12;
    const STEP_THIRTEEN = 13;

    const LAST_STEP = self::STEP_THIRTEEN;

    /**
     * Current step
     *
     * @var string
     */
    protected static $step;

    /**
     * Modules graph
     *
     * @var \Includes\Decorator\DataStructure\Graph\Modules
     */
    protected static $modulesGraph;

    /**
     * Get step
     *
     * @return mixed
     */
    public static function getStep()
    {
        return static::$step;
    }

    /**
     * Return modules graph
     *
     * @return \Includes\Decorator\DataStructure\Graph\Modules
     */
    public static function getModulesGraph()
    {
        if (!isset(static::$modulesGraph)) {
            static::$modulesGraph = \Includes\Decorator\Utils\Operator::createModulesGraph();
        }

        return static::$modulesGraph;
    }

    /**
     * Return classes repository path
     *
     * @return string
     */
    public static function getClassesDir()
    {
        return self::STEP_FIRST == static::$step
            ? LC_DIR_CLASSES
            : static::getCacheClassesDir();
    }

    /**
     * Get cache classes directory path
     *
     * @return string
     */
    public static function getCacheClassesDir()
    {
        return \Includes\Decorator\Utils\CacheManager::getCompileDir() . 'classes' . LC_DS;
    }

    /**
     * Get cache cLasses (models) directory path
     *
     * @return string
     */
    public static function getCacheModelsDir()
    {
        return static::getCacheClassesDir() . LC_NAMESPACE . LC_DS . 'Model' . LC_DS;
    }

    /**
     * Get cache cLasses (model proxies) directory path
     *
     * @return string
     */
    public static function getCacheModelProxiesDir()
    {
        return static::getCacheModelsDir() . 'Proxy' . LC_DS;
    }
}
