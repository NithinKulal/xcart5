<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Resources container routine
 */
abstract class AResourcesContainer extends \XLite\View\Container
{
    /**
     * Optimized resources
     *
     * @var array
     */
    protected static $optimizedResources = [];

    /**
     * Latest cache timestamp
     *
     * @var   integer
     */
    public static $latestCacheTimestamp;

    /**
     * Return cache dir path for resources
     *
     * @param array $params
     *
     * @return string
     */
    public static function getResourceCacheDir(array $params)
    {
        return LC_DIR_CACHE_RESOURCES . implode(LC_DS, $params). LC_DS;
    }

    /**
     * Return minified resources cache dir
     *
     * @param string $type Resource type, either 'js' or 'css'
     *
     * @return string
     */
    public static function getMinifiedCacheDir($type)
    {
        return LC_DIR_CACHE_RESOURCES . $type . LC_DS;
    }

    /**
     * Return CSS resources structure from the file cache
     *
     * @param array $resources
     *
     * @return array
     */
    public function getCSSResourceFromCache(array $resources)
    {
        return $this->getResourceFromCache(
            static::RESOURCE_CSS,
            $resources,
            array(
                static::RESOURCE_CSS,
                \XLite\Core\Request::getInstance()->isHTTPS() ? 'https' : 'http',
                $resources[0]['media'],
            ),
            'prepareCSSCache'
        ) + array('media' => $resources[0]['media']);
    }

    /**
     * Return latest time stamp of cache build procedure
     *
     * @return integer
     */
    protected static function getLatestCacheTimestamp()
    {
        if (!isset(\XLite\View\AResourcesContainer::$latestCacheTimestamp)) {
            \XLite\View\AResourcesContainer::$latestCacheTimestamp = intval(
                \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(\XLite::CACHE_TIMESTAMP)
            );
        }

        return \XLite\View\AResourcesContainer::$latestCacheTimestamp;
    }

    /**
     * Return JS resources structure from the file cache
     *
     * @param array $resources
     *
     * @return array
     */
    protected function getJSResourceFromCache(array $resources)
    {
        return $this->getResourceFromCache(static::RESOURCE_JS, $resources, array(static::RESOURCE_JS), 'prepareJSCache');
    }

    /**
     * Return resource structure from the file cache
     *
     * @param string $type                   File type of resource (js/css)
     * @param array  $resources              Resources for caching
     * @param array  $paramsForCache         Parameters of file cache (directory structure path to file)
     * @param string $prepareCacheFileMethod Method of $this object to read one resource entity and do some inner work if it is necessary
     *
     * @return array
     */
    protected function getResourceFromCache($type, array $resources, array $paramsForCache, $prepareCacheFileMethod)
    {
        $pathToCacheDir = static::getResourceCacheDir($paramsForCache);
        \Includes\Utils\FileManager::mkdirRecursive($pathToCacheDir);

        $file = hash('sha256', serialize($resources)) . '.' . $type;
        $filePath = $pathToCacheDir . $file;

        if (!\Includes\Utils\FileManager::isFile($filePath)) {
            $content = '';
            foreach ($resources as $resource) {
                $content .= $this->$prepareCacheFileMethod($resource);
            }
            \Includes\Utils\FileManager::write($filePath, $content);
        }

        return array(
            'file' => $filePath,
            'url'  => \XLite::getInstance()
                ->getShopURL(
                    str_replace(LC_DS, '/', substr($filePath, strlen(LC_DIR_ROOT))),
                    \XLite\Core\Request::getInstance()->isHTTPS()
                ),
        );
    }

    /**
     * Prepares CSS cache to use. Main issue - replace url($resourcePath) construction with url($shopUrl/$resourcePath)
     *
     * @param array $resource Array with CSS file data
     *
     * @return string
     */
    protected function prepareCSSCache($resource)
    {
        $data = '';
        if (isset($resource['file'])) {
            $filePath = $resource['file'];
            $minFilePath = str_replace(LC_DIR_SKINS, static::getMinifiedCacheDir(static::RESOURCE_CSS), $filePath);
            $minFilePath = dirname($minFilePath) . LC_DS . basename($minFilePath, '.css') . '.min.css';
            $minified = false;

            if (\Includes\Utils\FileManager::isFileReadable($minFilePath)) {
                $data = \Includes\Utils\FileManager::read($minFilePath);
                $minified = true;
            } else {
                $data = \Includes\Utils\FileManager::read($filePath);
            }

            $container = $this;
            $filePrefix = str_replace(LC_DS, '/', dirname(substr($filePath, strlen(LC_DIR_ROOT)))) . '/';
            $data = preg_replace_callback(
                '/url\(([^)]+)\)/Ss',
                function (array $matches) use ($container, $filePrefix) {
                    return $container->processCSSURLHandler($matches, $filePrefix);
                },
                $data
            );

            $noMinify = !empty($resource['no_minify']) || !empty($resource['no-minify']);

            if (!$minified && !$noMinify && strpos(basename($filePath), '.min.css') == false) {
                // Minify CSS content
                $data = $this->minifyCSS($data, $filePath);

                \Includes\Utils\FileManager::write($minFilePath, $data);
            }

            $data = trim($data);
        }

        return $data
            ? PHP_EOL . '/* AUTOGENERATED: ' . basename($filePath) . ' */' . PHP_EOL . $data
            : '';
    }

    /**
     * Get minified CSS content
     *
     * @param string $content  Source CSS content
     * @param string $filePath Source file path
     *
     * @return string
     */
    protected function minifyCSS($content, $filePath)
    {
        require_once LC_DIR_LIB . 'Minify' . LC_DS . 'cssmin.php';

        $minifier = new \CSSmin();

        return $minifier->run($content);
    }

    /**
     * Process CSS URL callback
     *
     * @param array  $mathes     Matches
     * @param string $filePrefix File prefix
     *
     * @return string
     */
    public function processCSSURLHandler(array $mathes, $filePrefix)
    {
        $url = trim($mathes[1]);

        if (!preg_match('/^[\'"]?data:/Ss', $url)) {
            $first = substr($url, 0, 1);

            if ('"' == $first || '\'' == $first) {
                $url = stripslashes(substr($url, 1, -1));
            }

            if (!preg_match('/^(?:https?:)?\/\//Ss', $url)) {
                if ('/' != substr($url, 0, 1)) {
                    $url = $filePrefix . $url;
                }

                $url = \Includes\Utils\URLManager::getProtoRelativeShopURL(
                    $url,
                    array(),
                    \Includes\Utils\URLManager::URL_OUTPUT_SHORT
                );
            }


            if (preg_match('/[\'"]/Ss', $url)) {
                $url = '"' . addslashes($url) . '"';
            }
        }

        return 'url(' . $url . ')';
    }

    /**
     * Prepares JS cache to use
     *
     * @param array $resource Array with JS file data
     *
     * @return string
     */
    protected function prepareJSCache($resource)
    {
        $data = '';
        if (isset($resource['file'])) {
            $filePath = $resource['file'];
            $minFilePath = str_replace(LC_DIR_SKINS, static::getMinifiedCacheDir(static::RESOURCE_JS), $filePath);
            $minFilePath = dirname($minFilePath) . LC_DS . basename($minFilePath, '.js') . '.min.js';
            $minified = false;

            // Get file content
            if (\Includes\Utils\FileManager::isFileReadable($minFilePath)) {
                $data = \Includes\Utils\FileManager::read($minFilePath);
                $minified = true;
            } else {
                $data = \Includes\Utils\FileManager::read($filePath);
            }

            $noMinify = !empty($resource['no_minify']) || !empty($resource['no-minify']);

            if (!$minified && !$noMinify && strpos(basename($filePath), '.min.js') == false) {
                // Minify js content
                $data = $this->minifyJS($data, $filePath);

                \Includes\Utils\FileManager::write($minFilePath, $data);
            }

            $data = trim($data);

            $data = preg_replace('/\)$/S', ');', $data);
        }

        return $data
            ? PHP_EOL . '/* AUTOGENERATED: ' . basename($filePath) . ' */' . PHP_EOL . $data . ';'
            : '';
    }

    /**
     * Make simple js-minification and return minified JS content
     *
     * @param string $content  Source JS content
     * @param string $filePath Source file path
     *
     * @return string
     */
    protected function minifyJS($content, $filePath)
    {
        require_once LC_DIR_LIB . 'Minify' . LC_DS . 'JSMinPlus.php';

        try {
            ob_start();
            $result = \JSMinPlus::minify($content);
            $error = ob_get_contents();
            ob_end_clean();

            if (false === $result) {
                throw new \Exception(sprintf('[%s] %s', $filePath, $error));
            }

        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->registerException($e);
            $result = $content;
        }

        return $result;
    }

    /**
     * Check if the CSS resources should be aggregated
     *
     * @return boolean
     */
    protected function doCSSAggregation()
    {
        return \XLite\Core\Config::getInstance()->Performance->aggregate_css;
    }

    /**
     * Check if the CSS resources should be aggregated
     *
     * @return boolean
     */
    protected function doCSSOptimization()
    {
        return (bool)\Includes\Utils\ConfigParser::getOptions(['storefront_options', 'optimize_css']);
    }

    /**
     * Check if the JS resources should be aggregated
     *
     * @return boolean
     */
    protected function doJSAggregation()
    {
        return \XLite\Core\Config::getInstance()->Performance->aggregate_js;
    }

    /**
     * Add specific unique identificator to resource URL
     *
     * @param string $url
     *
     * @return string
     */
    protected function getResourceURL($url)
    {
        return $url . (strpos($url, '?') === false ? '?' : '&') . static::getLatestCacheTimestamp();
    }

    /**
     * Get collected javascript resources
     *
     * @return array
     */
    protected function getJSResources()
    {
        return \XLite\Core\Layout::getInstance()->getPreparedResourcesByType(static::RESOURCE_JS);
    }

    /**
     * Get collected CSS resources
     *
     * @return array
     */
    protected function getCSSResources()
    {
        return \XLite\Core\Layout::getInstance()->getPreparedResourcesByType(static::RESOURCE_CSS);
    }

    /**
     * Resources must be grouped if the outer CSS or JS resource is used
     * For example:
     * array(
     *      controller.js,
     *      button.js,
     *      http://google.com/script.js,
     *      tail.js
     * )
     *
     * is grouped into:
     *
     * array(
     *      array(
     *          controller.js,
     *          button.js,
     *      ),
     *      array(http://google.com/script.js),
     *      array(
     *          tail.js
     *      )
     * )
     *
     * Then the local resources are cached according $cacheHandler method.
     *
     * @param array  $resources    Resources array
     * @param atring $cacheHandler Cache handler method
     *
     * @return array
     */
    public function groupResourcesByUrl($resources, $cacheHandler)
    {
        $groupByUrl = array();
        $group = array();

        foreach ($resources as $info) {
            if (0 === strpos($info['url'], '//')) {
                $info['url'] = (\XLite\Core\Request::getInstance()->isHTTPS() ? 'https:' : 'http:') . $info['url'];
            }

            $urlData = parse_url($info['url']);

            if (isset($urlData['host'])) {
                $groupByUrl = array_merge(
                    $groupByUrl,
                    empty($group) ? array() : array($this->$cacheHandler($group)),
                    array($info)
                );

                $group = array();
            } else {
                $group[] = $info;
            }
        }

        return array_merge($groupByUrl, empty($group) ? array() : array($this->$cacheHandler($group)));
    }

    /**
     * Get collected JS resources
     *
     * @return array
     */
    protected function getAggregateJSResources()
    {
        return $this->groupResourcesByUrl($this->getJSResources(), 'getJSResourceFromCache');
    }

    /**
     * Get collected CSS resources
     *
     * @return array
     */
    protected function getAggregateCSSResources()
    {
        $list = $this->getCSSResources();

        // Group CSS resources by media type
        $groupByMedia = array();

        foreach ($list as $fileInfo) {

            $index = (isset($fileInfo['interface']) && 'common' == $fileInfo['interface'] ? 'common-' : '')
                . (isset($fileInfo['media']) ? $fileInfo['media'] : 'all');

            $groupByMedia[$index][] = $fileInfo;
        }

        $list = array();
        foreach ($groupByMedia as $group) {
            $list = array_merge($list, $this->groupResourcesByUrl($group, 'getCSSResourceFromCache'));
        }

        return $list;
    }

    /**
     * Return style tag with content
     *
     * @param array $resource
     *
     * @return string
     */
    protected function getInternalCssByResource($resource)
    {
        if (!isset($resource['file'])) {
            return '';
        }

        $content = file_get_contents($resource['file']);

        if (isset($resource['media'])) {
            switch ($resource['media']) {
                case 'print':
                    return '';
                case 'all':
                    break;
                default:
                    $content = "@media {$resource['media']} {" . $content . '}';
            }
        }

        $content = "<style type='text/css'>" . $content . '</style>';

        return $content;
    }

    /**
     * Check if we need to "optimize" resource
     *
     * @param $resource
     *
     * @return bool
     */
    protected function isResourceSuitableForOptimization($resource)
    {
        if (isset($resource['file'])) {
            $resourceKey = md5(serialize($resource));
            $result = in_array($resourceKey, static::$optimizedResources) || !$this->isResourceKeyInCookie($resourceKey);

            if ($result) {
                $this->addResourceKeyToCookie($resourceKey);
                static::$optimizedResources[] = $resourceKey;

                return file_exists($resource['file']);
            }

        } else {
            return true;
        }

        return false;
    }

    /**
     * Add resource key to cookies
     *
     * @param $resourceKey
     */
    protected function addResourceKeyToCookie($resourceKey)
    {
        $request = \XLite\Core\Request::getInstance();

        $viewedResources = $request->viewedResources;

        if (!empty($viewedResources) && !is_array($viewedResources)) {
            $viewedResources = unserialize($viewedResources);
        }

        if (!is_array($viewedResources)) {
            $viewedResources = [];
        }

        $viewedResources[] = $resourceKey;
        $request->setCookie('viewedResources', serialize(array_unique($viewedResources)), 3600);
        $request->viewedResources = $viewedResources;
    }

    /**
     * Check if resource key is in cookies
     *
     * @param $resourceKey
     *
     * @return bool
     */
    protected function isResourceKeyInCookie($resourceKey)
    {
        $request = \XLite\Core\Request::getInstance();

        $viewedResources = $request->viewedResources;

        if (!empty($viewedResources) && !is_array($viewedResources)) {
            $viewedResources = unserialize($viewedResources);
        }

        if (is_array($viewedResources)) {
            return in_array($resourceKey, $viewedResources);
        }

        return false;
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }
}
