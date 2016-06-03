<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Widget cache manager serves as a factory of WidgetCache and also as an implementation of WidgetCacheRegistryInterface.
 *
 * TODO: current WidgetCacheRegistryInterface implementation is prone to race conditions, rewrite without separate registry file (for example, deleteAll can just remove cache entries having certain prefix).
 */
class WidgetCacheManager implements WidgetCacheRegistryInterface
{
    const REGISTRY_CELL = 'viewCacheRegistry';

    /**
     * Delete all
     *
     * @return boolean
     */
    public function deleteAll()
    {
        $result = true;
        foreach (array_keys($this->fetchRegistry()) as $key) {
            $result = $result && Database::getCacheDriver()->delete($key);
        }

        $this->saveRegistry(array());

        return $result;
    }

    /**
     * Set registry
     *
     * @param string $key        String-key
     * @param array  $parameters Cache cell keys
     *
     * @return void
     */
    public function setRegistry($key, array $parameters)
    {
        $registry = $this->fetchRegistry();

        $registry[$key] = $parameters;

        $this->saveRegistry($registry);
    }

    /**
     * Remove registry cell
     *
     * @param string $key Key
     *
     * @return void
     */
    public function unsetRegistry($key)
    {
        $registry = $this->fetchRegistry();

        if (isset($registry[$key])) {
            unset($registry[$key]);
        }

        $this->saveRegistry($registry);
    }

    /**
     * Get registry key
     *
     * @param array $parameters Cache cell keys (partial)
     *
     * @return array
     */
    public function getRegistryKey(array $parameters)
    {
        $registry = $this->fetchRegistry();

        $result = array();

        $keys = array();
        foreach ($keys as $k => $v) {
            if (is_null($v)) {
                unset($keys[$k]);
            }
        }

        foreach ($registry as $key => $parameters) {
            $found = true;
            foreach ($keys as $i => $value) {
                if (!isset($parameters[$i]) || $parameters[$i] != $value) {
                    $found = false;
                    break;
                }
            }

            if ($found) {
                $result[] = $key;
            }
        }

        return $result;
    }

    /**
     * Fetch registry
     *
     * @return array
     */
    public function fetchRegistry()
    {
        $registry = @unserialize(Database::getCacheDriver()->fetch(static::REGISTRY_CELL));
        if (!is_array($registry)) {
            $registry = array();
        }

        return is_array($registry) ? $registry : array();
    }

    /**
     * Save registry
     *
     * @param array $registry Registry
     *
     * @return void
     */
    public function saveRegistry(array $registry)
    {
        Database::getCacheDriver()->save(static::REGISTRY_CELL, serialize($registry));
    }

    /**
     * Invalidate widget cache based on entity types that were changed (inserted, updated or removed) during the current request.
     */
    public function invalidateBasedOnDatabaseChanges()
    {
        $notAffectingEntities = [
            'XLite\Model\TmpVar',
            'XLite\Model\Module',
            'XLite\Model\ModuleKey',
            'XLite\Model\Payment\Transaction',
            'XLite\Model\NotificationTranslation',
            'XLite\Model\Notification',
            'XLite\Model\ConfigTranslation',
        ];

        $entityTypes = Database::getRepo('XLite\Model\EntityTypeVersion')->getBumpedEntityTypes();

        if (array_diff($entityTypes, $notAffectingEntities)) {
            $this->deleteAll();
        }
    }
}