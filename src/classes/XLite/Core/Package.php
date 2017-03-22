<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Package operations
 */
class Package extends \XLite\Base\Singleton
{
    /**
     * This constant enables/disables logging the packing routine details
     */
    const PACKAGE_DEBUG_ENABLED = 0;

    /**
     * Minimum item weight
     *
     * @var float
     */
    protected $minimumItemWeight = 0.01;

    /**
     * Get packages array
     *
     * @param array $items  Array of ordered items
     * @param array $limits Array of package limits
     *
     * @return array
     */
    public function getPackages($items, $limits)
    {
        // Prepare items for packing
        $pendingItems = $this->initPendingItems($items);

        // Try to get packages from cache
        $key = $this->getCacheKey($pendingItems, $limits);

        $packages = $this->getCachedPackages($key);

        if (!$packages) {

            // Generate packages
            $packages = $this->generatePackages($pendingItems, $limits);

            if ($packages) {
                // Save packages in cache
                $this->savePackagesInCache($key, $packages);
            }
        }

        return $packages;
    }

    /**
     * Initialize pendingItems array
     *
     * @param array $items Array of ordered products
     *
     * @return array
     */
    protected function initPendingItems($items)
    {
        $pendingItems = array();

        if (is_array($items)) {
            foreach ($items as $item) {
                $product = $item->getProduct();

                if ($product) {
                    $weight = 0 === $item->getWeight()
                        ? $this->getMinimumItemWeight()
                        : $item->getWeight() / $item->getAmount();

                    $pendingItem = array(
                        'subtotal' => $item->getTotal(),
                        'price'    => $item->getItemNetPrice(),
                        'weight'   => $weight,
                        'qty'      => $item->getAmount(),
                        'id'       => $item->getItemId(),
                        'name'     => $product->getName(),
                    );

                    if ($product->getUseSeparateBox()) {
                        $pendingItem['separate_box'] = array(
                            'length' => $product->getBoxLength() * 1,
                            'width'  => $product->getBoxWidth() * 1,
                            'height' => $product->getBoxHeight() * 1,
                            'maxQty' => $product->getItemsPerBox(),
                        );
                    }

                    $pendingItems[] = $pendingItem;
                }
            }
        }

        return $pendingItems;
    }

    /**
     * Generate packages array
     *
     * @param array $pendingItems Array of pending items
     * @param array $limits       Array of limits
     *
     * @return array
     */
    protected function generatePackages($pendingItems, $limits)
    {
        // Save backup of pending items to make error message (if any) more clear
        $backupPendingItems = $pendingItems;

        $errorMsg = null;

        // Generate packages from items which must be shipped in separate boxes
        $separatePackages = $this->getSeparatePackages($pendingItems, $limits, $errorMsg);

        if (!$errorMsg) {
            // Generate packages from the rest items
            $restPackages = $this->packItems($pendingItems, $limits, $errorMsg);
        }

        if (!empty($pendingItems)) {
            \XLite\Logger::getInstance()->log($this->getLogMessage($backupPendingItems, $errorMsg), $this->getLogLevel());
        }

        if (self::PACKAGE_DEBUG_ENABLED) {
            // Log package details into var/log/PACKING-...
            $data = array(
                'pendingItems' => $pendingItems,
                'separatePackages' => $separatePackages,
                'restPackages' => $restPackages,
            );

            \XLite\Logger::logCustom('PACKING', $data);
        }

        // Return empty array on failure (if pending items list is not empty) or aggregated packages lists on success
        return $pendingItems
            ? array()
            : $this->getAggregatedPackages($separatePackages, $restPackages);
    }

    /**
     * Generate packages list from items which must be shipped in separate boxes
     *
     * @param array  &$pendingItems Array of pending items
     * @param array  $limits        Array of limits
     * @param string &$errorMsg     Error message
     *
     * @return array
     */
    protected function getSeparatePackages(&$pendingItems, $limits, &$errorMsg)
    {
        $packages = array();

        $items = $pendingItems;

        $stop = false;
        $id = 0;
        $packageId = 0;
        $e = null;

        while (!$stop) {

            if (empty($items[$id])) {
                $stop = true;

            } else {

                if (!empty($items[$id]['separate_box']) && 0 < $items[$id]['qty']) {

                    // Process next item of product
    
                    $item = $items[$id];

                    if (isset($packages[$packageId]['items'][$id])) {

                        // Product has already been partially packed

                        if ($packages[$packageId]['items'][$id]['qty'] < $item['separate_box']['maxQty']) {

                            $pack = $packages[$packageId];
                            $pack['items'][$id]['qty'] ++;
                            $pack['subtotal'] += $item['price'];
                            $pack['weight'] += $item['weight'];

                            if ($this->checkLimits($pack, $limits, $e)) {

                                // Move one item of product to the package
                                $packages[$packageId] = $pack;

                                // Decrease quantity of unpacked items
                                $items[$id]['qty'] --;

                            } else {
                                // Package limits failed - try to place item to a new package
                                $packageId ++;
                            }

                        } else {
                            // Package is full, add new
                            $packageId ++;
                        }

                    } else {

                        // Create package and add first item of product
                        $item['qty'] = 1;
                        $pack = array();
                        $pack['items'][$id] = $item;
                        $pack['subtotal'] = $item['price'];
                        $pack['weight'] = $item['weight'];
                        $pack['box'] = $item['separate_box'];

                        if ($this->checkLimits($pack, $limits, $e)) {
                            $packages[$packageId] = $pack;

                            // Decrease quantity of unpacked items
                            $items[$id]['qty'] --;

                        } else {

                            // Package limits failed - it's impossible to pack item for delivery

                            $stop = true;

                            if ($e) {
                                $errorMsg = $e;
                            }
                        }
                    }

                } else {

                    if (0 == $items[$id]['qty']) {

                        // Remove completely packed product
                        unset($items[$id]);

                        // Create new package for the next product
                        $packageId ++;
                    }

                    // Pass to the next product
                    $id ++;
                }
            }
        }

        $pendingItems = $items;

        return $packages;
    }

    /**
     * Generate packages from the items
     *
     * @param array  &$pendingItems Array of pending items
     * @param array  $limits        Array of limits
     * @param string &$errorMsg     Error message
     *
     * @return array
     */
    protected function packItems(&$pendingItems, $limits, &$errorMsg)
    {
        $packages = array();

        $items = $this->preprocessPendingItems($pendingItems);

        $maxItemId = count($items) - 1;

        $stop = false;
        $id = 0;
        $packageId = 0;
        $error = false;
        $e = null;

        while (!$stop) {

            if (empty($items) || $error) {
                $stop = true;

            } else {

                if (isset($items[$id]) && 0 < $items[$id]['qty']) {

                    // Process next item of product
    
                    $item = $items[$id];

                    // Product item is satisfied to the package limits
    
                    if (isset($packages[$packageId]['items'][$id])) {

                        // Product has already been partially packed
                        // Add new item of product to the package

                        $pack = $packages[$packageId];

                        $pack['items'][$id]['qty'] ++;
                        $pack['subtotal'] += $item['price'];
                        $pack['weight'] += $item['weight'];

                        if ($this->checkLimits($pack, $limits, $e)) {

                            // Save package if it is satisfied to the limits 
                            $packages[$packageId] = $pack;

                            // Decrease quantity of unpacked items
                            $items[$id]['qty'] --;

                        } else {

                            // Package limits failed - go to the next product
                            $id ++;
                        }

                    } else {

                        // Create package and add first item of product

                        $item['qty'] = 1;

                        $isNewPackage = false;

                        if (!isset($packages[$packageId])) {
                            $pack = array();
                            $pack['items'] = array();
                            $pack['subtotal'] = 0;
                            $pack['weight'] = 0;
                            $pack['box'] = $limits;

                            $isNewPackage = true;

                        } else {
                            $pack = $packages[$packageId];
                        }

                        $pack['items'][$id] = $item;
                        $pack['subtotal'] += $item['price'];
                        $pack['weight'] += $item['weight'];

                        if ($this->checkLimits($pack, $limits, $e)) {

                            // Save package if it is satisfied to the limits 
                            $packages[$packageId] = $pack;

                            // Decrease quantity of unpacked items
                            $items[$id]['qty'] --;

                        } else {

                            // Package limits failed

                            if ($isNewPackage) {

                                // It's impossible to place item into new (empty) package - break operation
                                $error = true;

                                if ($e) {
                                    $errorMsg = $e;
                                }

                            } else {
                                // Go to the next product
                                $id ++;
                            }
                        }
                    }

                } else {

                    // Remove completely packed product
                    if (isset($items[$id]) && 0 == $items[$id]['qty']) {
                        unset($items[$id]);
                    }

                    // Increase $id and pass to the next product
                    $id ++;
                }

                if ($id > $maxItemId) {

                    $id = 0;

                    // Create new package if there are no items added in the last cycle
                    $packageId ++;
                }
            }
        }

        if (!$error) {
            $pendingItems = $items;
        }

        return $packages;
    }

    /**
     * Get aggregated packages from two packages lists
     *
     * @param array $packages1 First array of packages
     * @param array $packages2 Second array of packages
     *
     * @return array
     */
    protected function getAggregatedPackages($packages1, $packages2)
    {
        $result = $packages1;

        foreach ($packages2 as $pack) {
            $result[] = $pack;
        }

        return $result;
    }

    /**
     * Sort pending items array by weight and normalize array keys
     *
     * @param array $pendingItems Array of pending items
     *
     * @return array
     */
    protected function preprocessPendingItems($pendingItems)
    {
        usort($pendingItems, [$this, 'sortByWeight']);

        return $pendingItems;
    }

    /**
     * Callback method for sorting out items by weight
     *
     * @param array $a First compared item
     * @param array $b Second compared item
     *
     * @return integer
     */
    public function sortByWeight($a, $b)
    {
        if ($a['weight'] == $b['weight']) {
            $result = 0;

        } elseif ($a['weight'] > $b['weight']) {
            $result = -1;

        } else {
            $result = 1;
        }

        return $result;
    }

    /**
     * Check box for limits and return true if box is satisfied limits
     *
     * @param array  $box    Box properties
     * @param array  $limits Array of limits
     * @param string &$error Error message
     *
     * @return boolean
     */
    protected function checkLimits($box, $limits, &$error)
    {
        $result = true;

        $error = null;

        if (!empty($limits) && is_array($limits)) {

            foreach ($limits as $key => $limit) {

                if (isset($box[$key]) && $box[$key] > $limit) {
                    $result = false;
                    $error = sprintf('Limit failure: %s = %s (limit is %s)', $key, $box[$key], $limit);
                    break;
                }
            }
        }

        return $result;
    }

    // {{{ Cache

    /**
     * Get cache key
     *
     * @param array $pendingItems Array of pending items
     * @param array $limits       Array of limits
     *
     * @return string
     */
    protected function getCacheKey($pendingItems, $limits)
    {
        $keyData = array(
            'pendingItems' => $pendingItems,
            'limits'       => $limits,
        );

        return 'pack-' . md5(serialize($keyData));
    }

    /**
     * Get cached packages by key
     *
     * @param string $key Cache key
     *
     * @return array
     */
    protected function getCachedPackages($key)
    {
        $result = array();

        $cacheDriver = \XLite\Core\Database::getCacheDriver();

        if ($cacheDriver->contains($key)) {
            $result = $cacheDriver->fetch($key);
        }

        return $result;
    }

    /**
     * Save packages in cache
     *
     * @param string $key  Cache key
     * @param array  $data Data to save in cache
     *
     * @return void
     */
    protected function savePackagesInCache($key, $data)
    {
        \XLite\Core\Database::getCacheDriver()->save($key, $data);
    }

    // }}}

    /**
     * Return log message
     *
     * @param array  $pendingItems Array of pending items
     * @param string $errorMsg     Error message
     *
     * @return string
     */
    protected function getLogMessage($pendingItems, $errorMsg)
    {
        $items = array();

        foreach ($pendingItems as $id => $item) {
            $items[] = sprintf('%s (weight: %0.2f, qty: %d)', $item['id'], $item['weight'], $item['qty']);
        }

        $message = 'Failure to pack items: ' . implode(', ', $items) . PHP_EOL . $errorMsg;

        return $message;
    }

    /**
     * Return type of log messages
     *
     * @return integer
     */
    protected function getLogLevel()
    {
        return LOG_WARNING;
    }

    // {{{ Minimum item weight

    /**
     * Returns minimum item weight
     *
     * @param float $weight Weight
     */
    public function setMinimumItemWeight($weight)
    {
        $this->minimumItemWeight = $weight;
    }

    /**
     * Returns minimum item weight
     *
     * @return float
     */
    protected function getMinimumItemWeight()
    {
        return $this->minimumItemWeight;
    }

    // }}}
}
