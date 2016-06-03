<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Templating;

use Includes\Utils\FileManager;
use XLite\Core\Templating\Twig\Loader\FilesystemAbsolutePath;


/**
 * Twig CacheManagerInterface implementation
 */
class TwigCacheManager extends AbstractTwigEngine implements CacheManagerInterface
{
    protected static $instance;

    public function __construct()
    {
        parent::__construct(new FilesystemAbsolutePath());
    }

    /**
     * Invalidates a filesystem cache for the specified template file
     *
     * (FilesystemCacheControlInterface)
     *
     * @param string $templateFile Full path to template
     */
    public function invalidate($templateFile)
    {
        $key = $this->getCacheFilename($templateFile);

        if ($key !== false) {
            FileManager::deleteFile($key);
        }
    }

    /**
     * Warms up cache for the specified template file
     *
     * @param string $templateFile Full path to template
     */
    public function warmup($templateFile)
    {
        $key = $this->getCacheFilename($templateFile);

        if ($key !== false) {
            FileManager::write(
                $key,
                $this->twig->compileSource(FileManager::read($templateFile), $templateFile)
            );
        }
    }

    /**
     * Gets the cache filename for a given template.
     *
     * @param string $path The template path
     *
     * @return string|false The cache file name or false when caching is disabled
     */
    protected function getCacheFilename($path)
    {
        return $this->twig->getCache(false)->generateKey(
            $path,
            $this->twig->getTemplateClass($path)
        ) ?: false;
    }
}