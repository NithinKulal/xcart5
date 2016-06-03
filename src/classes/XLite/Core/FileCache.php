<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * File system cache
 * FIXME: must be completely refactored
 */
class FileCache extends \Doctrine\Common\Cache\CacheProvider
{
    /**
     * Cache directory path
     *
     * @var string
     */
    protected $path;

    /**
     * File header
     *
     * @var string
     */
    protected $header = '<?php die(); ?>';

    /**
     * File header length
     *
     * @var integer
     */
    protected $headerLength = 15;

    /**
     * TTL block length
     *
     * @var integer
     */
    protected $ttlLength = 11;

    /**
     * Validation cache
     *
     * @var array
     */
    protected $validationCache = array();

    /**
     * Namespace
     *
     * @var string
     */
    protected $_namespace;

    /**
     * Constructor
     *
     * @param string $path Path
     */
    public function __construct($path = null)
    {
        $this->setPath($path ?: sys_get_temp_dir());
    }

    /**
     * Check - cache provider is valid or not
     *
     * @return boolean
     */
    public function isValid()
    {
        return (bool)$this->getPath();
    }

    /**
     * Set cache path
     *
     * @param string $path Path
     *
     * @return void
     */
    public function setPath($path)
    {
        if (is_string($path)) {
            if (!file_exists($path)) {
                \Includes\Utils\FileManager::mkdirRecursive($path);
            }

            if (file_exists($path) && is_dir($path) && is_writable($path)) {
                $this->path = $path;
            }
        }
    }

    /**
     * Get cache path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return parent::fetch($this->getNamespaceIdHash($id));
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return parent::contains($this->getNamespaceIdHash($id));
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return parent::save($this->getNamespaceIdHash($id), $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return parent::delete($this->getNamespaceIdHash($id));
    }

    /**
     * getNamespacedId
     * @fixme: parent is private
     *
     * @param string $id ____param_comment____
     *
     * @return string
     */
    protected function getNamespacedId($id)
    {
        $namespaceCacheKey = sprintf(static::DOCTRINE_NAMESPACE_CACHEKEY, $this->getNamespace());
        $namespaceVersion  = ($this->doContains($namespaceCacheKey)) ? $this->doFetch($namespaceCacheKey) : 1;

        return sprintf('%s[%s][%s]', $this->getNamespace(), $id, $namespaceVersion);
    }

    /**
     * Get hash of namespace id
     *
     * @param string $id The id to namespace.
     *
     * @return string
     */
    protected function getNamespaceIdHash($id)
    {
        return !LC_CACHE_NAMESPACE_HASH ? $id : ('md5.' . md5($id));
    }

    /**
     * getNamespacedId
     *
     * @param string $id ____param_comment____
     *
     * @return string
     */
    protected function getNamespacedIdToDelete($id)
    {
        return sprintf('%s[%s*', $this->getNamespace(), $id);
    }

    /**
     * Get cache cell by id
     *
     * @param string $id CEll id
     *
     * @return mixed
     */
    protected function doFetch($id)
    {
        $path = $this->getPathById($id);

        $result = false;

        if (file_exists($path) && $this->isKeyValid($path)) {
            $result = unserialize(file_get_contents($path, false, null, $this->headerLength + $this->ttlLength));
        }

        return $result;
    }

    /**
     * Check - repository has cell with specified id or not
     *
     * @param string $id CEll id
     *
     * @return boolean
     */
    protected function doContains($id)
    {
        $path = $this->getPathById($id);

        return file_exists($path) && $this->isKeyValid($path);
    }

    /**
     * Save cell data
     *
     * @param string  $id       Cell id
     * @param mixed   $data     Cell data
     * @param integer $lifeTime Cell TTL OPTIONAL
     *
     * @return boolean
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $lifeTime = max(0, (int) $lifeTime);
        if ($lifeTime) {
            $lifeTime += LC_START_TIME;
        }

        $lifeTime = (string) $lifeTime;

        return \Includes\Utils\FileManager::write(
            $this->getPathById($id),
            $this->header . str_repeat(' ', $this->ttlLength - strlen($lifeTime)) . $lifeTime . serialize($data)
        );
    }

    /**
     * Delete cell
     *
     * @param string $id Cell id
     *
     * @return boolean
     */
    protected function doDelete($id)
    {
        $path = $this->getPathById($id);

        $result = false;

        if (file_exists($path)) {
            $result = @unlink($path);
        }

        return $result;
    }

    /**
     * doFlush
     *
     * @return boolean
     */
    protected function doFlush()
    {
        return true;
    }

    /**
     * doGetStats
     *
     * @return array
     */
    protected function doGetStats()
    {
        return array();
    }

    /**
     * Get cell path by cell id
     *
     * @param string $id Cell id
     *
     * @return string
     */
    protected function getPathById($id)
    {
        return $this->path . LC_DS . str_replace(array('\\', '..', LC_DS), '_', $id) . '.php';
    }

    /**
     * Check - cell file is valid or not
     *
     * @param string $path CEll file path
     *
     * @return boolean
     */
    protected function isKeyValid($path)
    {
        if (!isset($this->validationCache[$path]) || !$this->validationCache[$path]) {
            $result = true;

            $ttl = (int) file_get_contents($path, false, null, $this->headerLength, $this->ttlLength);

            if (0 < $ttl && \XLite\Core\Converter::time() > $ttl) {
                unlink($path);
                $result = false;
            }

            $this->validationCache[$path] = $result;
        }

        return $this->validationCache[$path];
    }
}
