<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating;

use XLite\Core\Layout;


/**
 * Twig templating engine factory
 */
class TwigEngineFactory
{
    /**
     * Runtime cached engines
     *
     * @var array
     */
    protected static $engines = array();

    /**
     * @var Layout
     */
    private $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    public function getEngine()
    {
        $paths = $this->getPaths();

        $cacheKey = md5(serialize($paths));

        $engine = $this->tryGetFromCache($cacheKey);

        if ($engine === null) {
            $engine = new TwigEngine($paths);
            $this->saveInCache($cacheKey, $engine);
        }

        return $engine;
    }

    protected function tryGetFromCache($cacheKey)
    {
        return isset(static::$engines[$cacheKey])
            ? static::$engines[$cacheKey]
            : null;
    }

    protected function saveInCache($cacheKey, $engine)
    {
        static::$engines[$cacheKey] = $engine;
    }

    protected function getPaths()
    {
        $skins = $this->layout->getSkinPaths();

        $skins = array_merge(
            $skins,
            $this->layout->getSkinPaths('common')
        );

        $paths = array_map(function ($skin) {
            return $skin['fs'];
        }, $skins);

        return array_filter($paths, 'file_exists');
    }
}
