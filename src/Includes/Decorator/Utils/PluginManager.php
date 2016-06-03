<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\Utils;

/**
 * PluginManager 
 *
 */
abstract class PluginManager extends \Includes\Decorator\Utils\AUtils
{
    /**
     * Config file name
     */
    const FILE_INI = 'plugins.ini';

    /**
     * List of registered plugins
     *
     * @var array
     */
    protected static $plugins;

    /**
     * Check and execute hook handlers
     *
     * @param string $hook Hook name
     *
     * @return void
     */
    public static function invokeHook($hook)
    {
        // Get plugins "subscribed" for the hook
        foreach (static::getPlugins($hook) as $plugin => $instance) {

            if (!isset($instance)) {
                $class = '\Includes\Decorator\Plugin\\' . str_replace('_', '\\', $plugin) . '\Main';
                static::$plugins[$plugin] = $instance = new $class();
            }

            if ($instance->isBlockingPlugin()) {
                // Block software before run plugin
                \Includes\Decorator\Utils\CacheManager::setRebuildBlockMark(
                    \Includes\Decorator\Utils\CacheManager::getStep(),
                    array(
                        'hook'  => $hook,
                        'class' => get_class($instance),
                    )
                );
            }

            // Show message
            $title = $instance->getTitle() ?: ('Run the "' . $plugin . '" plugin...');
            \Includes\Decorator\Utils\CacheManager::showStepMessage($title);

            // Execute plugin main method
            $instance->executeHookHandler();

            // Show memory usage
            \Includes\Decorator\Utils\CacheManager::showStepInfo();
        }
    }

    /**
     * Return list of registered plugins
     *
     * @param string $hook Hook name OPTIONAL
     *
     * @return array
     */
    protected static function getPlugins($hook = null)
    {
        if (!isset(static::$plugins)) {

            // Check config file
            if (\Includes\Utils\FileManager::isFileReadable(static::getConfigFile())) {

                // Iterate over all sections
                foreach (parse_ini_file(static::getConfigFile(), true) as $section => $plugins) {

                    // Set plugins order
                    asort($plugins, SORT_NUMERIC);

                    // Save plugins list
                    static::$plugins[$section] = array_fill_keys(array_keys($plugins), null);
                }

            } else {
                \Includes\ErrorHandler::fireError('Unable to read config file for the Decorator plugins');
            }
        }

        return \Includes\Utils\ArrayManager::getIndex(static::$plugins, $hook);
    }

    /**
     * Return configuration file
     *
     * @return string
     */
    protected static function getConfigFile()
    {
        return LC_DIR_INCLUDES . 'Decorator' . LC_DS . static::FILE_INI;
    }
}
