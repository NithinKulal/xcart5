<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * ConfigParser
 *
 * @package    XLite
 */
abstract class ConfigParser extends \Includes\Utils\AUtils
{
    /**
     * Options cache
     *
     * @var array
     */
    protected static $options;

    /**
     * List of function to modify options
     *
     * @var array
     */
    protected static $mutators = array(
        'setWebDirWOSlash',
        'adjustHost',
    );

    /**
     * List of additional source files for options gathering
     *
     * @var array
     */
    protected static $configFiles = array(
        'config.php',
        'config.personal.php',
        'config.local.php',
    );

    /**
     * Return path to the main config file
     *
     * @return string
     */
    protected static function getMainFile()
    {
        return LC_DIR_CONFIG . 'config.default.php';
    }

    /**
     * Throw the exception if config file is not found
     *
     * @param string $file file which caused an error
     *
     * @return void
     */
    protected static function handleFileAbsenceError($file)
    {
        throw new \Exception('Config file "' . $file . '" does not exist or is not readable');
    }

    /**
     * Throw the exception if unable to parse config file
     *
     * @param string $file file which caused an error
     *
     * @return void
     */
    protected static function handleFileWrongFormatError($file)
    {
        throw new \Exception('Unable to parse config file "' . $file . '" (probably it has a wrong format)');
    }

    /**
     * Check if file exists and is readable
     *
     * @param string $file file to check
     *
     * @return bool
     */
    protected static function checkFile($file)
    {
        return \Includes\Utils\FileManager::isFileReadable($file);
    }

    /**
     * Common function to parse config files
     *
     * @param string $file         file to parse
     * @param string $errorHandler name of error handler (method)
     *
     * @return array
     */
    protected static function parseCommon($file, $errorHandler = null)
    {
        $options = array();

        if (static::checkFile($file)) {
            if (!is_array($options = parse_ini_file($file, true))) {
                static::handleFileWrongFormatError($file);
            }
        } elseif (isset($errorHandler)) {
            static::$errorHandler($file);
        }

        return $options;
    }

    /**
     * Parse main config file
     *
     * @return array
     */
    protected static function parseMainFile()
    {
        return static::parseCommon(static::getMainFile(), 'handleFileAbsenceError');
    }

    /**
     * Parse local config file
     *
     * @return array
     */
    protected static function parseLocalFile($fileName)
    {
        return static::parseCommon(LC_DIR_CONFIG . LC_DS . $fileName);
    }

    /**
     * Fetch options from array
     *
     * @param array $names   option names tree
     * @param array $options options list
     *
     * @return array|mixed
     */
    protected static function getOptionsByNames(array $names, $options)
    {
        $name = array_shift($names);
        $options = empty($name) ? $options : (empty($options[$name]) ? null : $options[$name]);

        return empty($names) ? $options : static::getOptionsByNames($names, $options);
    }

    /**
     * Exceute the mutators stack
     *
     * @return void
     */
    protected static function executeMutators()
    {
        foreach (static::$mutators as $method) {
            static::$method();
        }
    }

    /**
     * Create the "web_dir_wo_slash" option
     *
     * @return void
     */
    protected static function setWebDirWOSlash()
    {
        static::$options['host_details']['web_dir_wo_slash']
            = \Includes\Utils\URLManager::trimTrailingSlashes(static::$options['host_details']['web_dir']);
    }

    /**
     * Adjust hosts if site is requested from different host and alternative domains are specified
     *
     * @return void
     */
    protected static function adjustHost()
    {
        foreach (array('http_host', 'https_host') as $host) {

            if (
                !empty(static::$options['host_details']['domains'])
                && isset($_SERVER['HTTP_HOST'])
                && static::$options['host_details'][$host] != $_SERVER['HTTP_HOST']
                && in_array($_SERVER['HTTP_HOST'], explode(',', static::$options['host_details']['domains']))
            ) {
                static::$options['host_details'][$host . '_orig'] = static::$options['host_details'][$host];
                static::$options['host_details'][$host] = $_SERVER['HTTP_HOST'];
            }
        }
    }

    /**
     * Parse both config files
     *
     * @param array|string $names option names tree
     *
     * @return array|mixed
     */
    public static function getOptions($names = null)
    {
        if (!isset(static::$options)) {

            static::$options = static::parseMainFile();

            for ($i = 0; $i < count(static::$configFiles); $i++) {
                static::$options = array_replace_recursive(static::$options, static::parseLocalFile(static::$configFiles[$i]));
            }

            static::executeMutators();
        }

        return static::getOptionsByNames(is_array($names) ? $names : array($names), static::$options);
    }

    /**
     * Register additional config file
     *
     * @param string $fileName Config file name
     *
     * @return void
     */
    public static function registerConfigFile($fileName)
    {
        if (false === array_search($fileName, static::$configFiles)) {
            static::$configFiles[] = $fileName;
            static::$options = null;
        }
    }

    /**
     * The installation language code which is given in the config files (en, ru)
     *
     * @return string
     */
    public static function getInstallationLng()
    {
        return static::getOptions(array('installation', 'installation_lng'));
    }
}
