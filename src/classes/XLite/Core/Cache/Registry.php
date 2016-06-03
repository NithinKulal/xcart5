<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Cache;

/**
 * Cache registry
 */
class Registry extends \XLite\Base implements \Doctrine\Common\Cache\Cache
{
    const CELL_REGISTRY = 'registry';

    /**
     * Namespace
     *
     * @var string
     */
    protected $namespace;

    /**
     * Cache driver
     *
     * @var \XLite\Core\Cache
     */
    protected $driver;

    /**
     * Constructor
     *
     * @param string            $namespace Namesapce
     * @param \XLite\Core\Cache $driver    Driver OPTIONAL
     *
     * @return void
     */
    public function __construct($namespace, \XLite\Core\Cache $driver = null)
    {
        $this->namespace = $namespace;
        $this->driver = $driver ?: \XLite\Core\Database::getCacheDriver();
    }

    /**
     * Call driver's method
     *
     * @param string $name      Method name
     * @param array  $arguments Arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($name, array $arguments = array())
    {
        return call_user_func_array(array($this->driver, $name), $arguments);
    }

    // {{{ Routines

    /**
     * Get cell
     * 
     * @param string $id Cell id
     *
     * @return mixed
     */
    public function fetch($id)
    {
        return $this->driver->fetch($this->assembleId($id));
    }

    /**
     * Set cell
     * 
     * @param string  $id       Cell id
     * @param mixed   $data     Cell data
     * @param integer $lifeTime Life time OPTIONAL
     *
     * @return void
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $key = $this->assembleId($id);
        $registry = $this->getRegistry();
        $registry[$id] = $key;
        $this->driver->save($key, $data);
        $this->setRegistry($registry);
    }

    /**
     * Check cell existing
     * 
     * @param string $id Cell id
     *
     * @return boolean
     */
    public function contains($id)
    {
        return $this->driver->contains($this->assembleId($id));
    }

    /**
     * Delete cell
     * 
     * @param string $id Cell id
     *
     * @return void
     */
    public function delete($id)
    {
        $this->driver->delete($this->assembleId($id));
        $registry = $this->getRegistry();
        if (isset($registry[$id])) {
            unset($registry[$id]);
            $this->setRegistry($registry);
        }
    }

    /**
     * Get statistics
     *
     * @return array
     */
    public function getStats()
    {
        return $this->driver->getStats();
    }

    /**
     * Delete all cells
     *
     * @return void
     */
    public function deleteAll()
    {
        foreach ($this->getRegistry() as $id) {
            $this->driver->delete($id);
        }
        $this->setRegistry(array());
    }

    /**
     * Get all cells id's
     *
     * @return array
     */
    public function getIds()
    {
        return array_keys($this->getRegistry());
    }

    /**
     * Get storage registry 
     * 
     * @return array
     */
    protected function getRegistry()
    {
        $registry = $this->driver->fetch($this->assembleServiceId(static::CELL_REGISTRY));

        return is_array($registry) ? $registry : array();
    }

    /**
     * Set storage registry 
     * 
     * @param array $registry Storage registry
     *  
     * @return void
     */
    protected function setRegistry(array $registry)
    {
        $this->driver->save($this->assembleServiceId(static::CELL_REGISTRY), $registry);
    }

    /**
     * Assemble storage cell id
     * 
     * @param string $suffix Internal cell id
     *  
     * @return string
     */
    protected function assembleId($suffix)
    {
        return $this->namespace . '.' . $suffix;
    }

    /**
     * Assemble service cell id
     * 
     * @param string $suffix Service key
     *  
     * @return string
     */
    protected function assembleServiceId($suffix)
    {
        return $this->namespace . '_' . $suffix;
    }

    // }}}

}
