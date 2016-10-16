<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes;

/**
 * Safe Mode
 *
 * :TODO: reduce numder of public methods
 *
 */
abstract class SafeMode
{
    /**
     * Request params
     */
    const PARAM_SAFE_MODE  = 'safe_mode';
    const PARAM_ACCESS_KEY = 'access_key';
    const PARAM_SOFT_RESET = 'soft_reset';
    const PARAM_DROP_CACHE_MODE  = 'drop_cache';
    const PARAM_RESTORE_DATE  = 'date';

    /**
     * Soft reset label
     */
    const LABEL_SOFT_RESET = 'Soft reset';

    /**
     * Restore date start
     */
    const LABEL_RESTORE_DATE = 'restore_from=';

    /**
     * Modules list file name
     */
    const UNSAFE_MODULES_FILE_NAME = '.decorator.unsafe_modules.ini.php';


    /**
     * Return true if software reset is enabled in config file
     *
     * @return boolean
     */
    public static function isSoftwareResetEnabled()
    {
        return !(bool) \Includes\Utils\ConfigParser::getOptions(array('decorator', 'disable_software_reset'));
    }

    /**
     * Check request parameters
     *
     * @return void
     */
    public static function isSafeModeRequested()
    {
        return static::checkAccessKey() && isset($_GET[self::PARAM_SAFE_MODE]);
    }

    /**
     * Check request parameters
     *
     * @return boolean
     */
    public static function isRestoreDateSet()
    {
        $date = static::getRestoreDateFromIndicator();
        return (static::checkAccessKey() && isset($_GET[self::PARAM_RESTORE_DATE])) || $date !== null;
    }

    /**
     * Check request parameters
     *
     * @return void
     */
    public static function isDropCacheRequested()
    {
        return static::checkAccessKey() && isset($_GET[self::PARAM_DROP_CACHE_MODE]);
    }

    /**
     * Check if the safe mode requested in the "Soft reset" variant
     *
     * @return boolean
     */
    public static function isSoftResetRequested()
    {
        return 0 < strpos(\Includes\Utils\FileManager::read(static::getIndicatorFileName()), self::LABEL_SOFT_RESET);
    }

    /**
     * Check request parameters
     *
     * @return boolean
     */
    public static function isSafeModeStarted()
    {
        return \Includes\Utils\FileManager::isFile(static::getIndicatorFileName());
    }

    /**
     * Get restore date param
     *
     * @return string
     */
    public static function getRestoreDate()
    {
        return isset($_GET[self::PARAM_RESTORE_DATE]) ? $_GET[self::PARAM_RESTORE_DATE] : static::getRestoreDateFromIndicator();
    }

    /**
     * Get restore date param
     *
     * @return string
     */
    public static function getRestoreDateFromIndicator()
    {
        $date = null;
        $indicator = \Includes\Utils\FileManager::read(static::getIndicatorFileName());
        $search = strpos($indicator, self::LABEL_RESTORE_DATE);
        if ($search !== false) {
            $datePos = $search + strlen(self::LABEL_RESTORE_DATE);
            $date = substr($indicator, $datePos);            
        } 
        return $date;
    }

    /**
     * Get Access Key
     *
     * @return string
     */
    public static function getAccessKey()
    {
        if (!\Includes\Utils\FileManager::isFile(static::getAccessKeyFileName())) {
            static::regenerateAccessKey();
        }

        return \Includes\Utils\FileManager::read(static::getAccessKeyFileName());
    }

    /**
     * Re-generate access key
     *
     * @param boolean $sendNotification Flag if the notification must be sent OPTIONAL
     *
     * @return void
     */
    public static function regenerateAccessKey($sendNotification = false)
    {
        // Put access key file
        \Includes\Utils\FileManager::write(static::getAccessKeyFileName(), static::generateAccessKey());

        // Send email notification
        if ($sendNotification) {
            static::sendNotification(true);
        }
    }

    /**
     * Send email notification to administrator about access key
     *
     * @param boolean $keyChanged is access key was changed
     *
     * @return void
     */
    public static function sendNotification($keyChanged = false)
    {
        if (!\Includes\Decorator\Utils\CacheManager::isRebuildNeeded(\Includes\Decorator\Utils\CacheManager::STEP_THIRD)) {
            // Send email notification
            \XLite\Core\Mailer::sendSafeModeAccessKeyNotification(
                \Includes\Utils\FileManager::read(static::getAccessKeyFileName()),
                $keyChanged
            );
        }
    }

    /**
     * Send email notification to administrator about access key during upgrade
     *
     * @param boolean $keyChanged is access key was changed
     *
     * @return void
     */
    public static function sendUpgradeNotification()
    {
        if (!\Includes\Decorator\Utils\CacheManager::isRebuildNeeded(\Includes\Decorator\Utils\CacheManager::STEP_THIRD)) {
            // Send email notification
            \XLite\Core\Mailer::sendUpgradeSafeModeAccessKeyNotification();
        }
    }

    /**
     * Get safe mode URL
     *
     * @param boolean $soft Soft reset flag OPTIONAL
     *
     * @return string
     */
    public static function getResetURL($soft = false)
    {
        $params = array(
            self::PARAM_SAFE_MODE  => true,
            self::PARAM_ACCESS_KEY => static::getAccessKey()
        );

        if ($soft) {
            $params[self::PARAM_SOFT_RESET] = true;
        }

        return \Includes\Utils\URLManager::getShopURL(
            \XLite\Core\Converter::buildURL('main', '', $params, \XLite::getAdminScript())
        );
    }

    public static function getLatestSnapshot()
    {
        $result = null;
        $snapshots = \Includes\Utils\ModulesManager::readModuleMigrationLog();
        while (!\Includes\Utils\ModulesManager::isRestorePointValid($result) && $snapshots && is_array($snapshots) && count($snapshots) > 0) {
            $result = array_pop($snapshots);
        }

        if (!\Includes\Utils\ModulesManager::isRestorePointValid($result)) {
            $result = null;
        }
        return $result;
    }

    /**
     * Get latest Snapshot URL
     *
     * @return string
     */
    public static function getLatestSnapshotURL()
    {
        $params = array(
            self::PARAM_SAFE_MODE  => true,
            self::PARAM_ACCESS_KEY => static::getAccessKey(),
        );

        if (static::getLatestSnapshot()) {
            $latest = static::getLatestSnapshot();
            $params[self::PARAM_RESTORE_DATE] = $latest['date'];
        }

        return \Includes\Utils\URLManager::getShopURL(
            \XLite\Core\Converter::buildURL('main', '', $params, \XLite::getAdminScript())
        );
    }

    /**
     * Clean up the safe mode indicator
     *
     * @return void
     */
    public static function cleanupIndicator()
    {
        \Includes\Utils\FileManager::deleteFile(static::getIndicatorFileName());
    }

    /**
     * Initialization
     *
     * @return void
     */
    public static function initialize()
    {
        if (static::isDropCacheRequested()) {

            // Drop classes cache

            \Includes\Decorator\Utils\CacheManager::cleanupCacheIndicators();

            // Redirect to avoid loop
            \Includes\Utils\Operator::redirect(\XLite::getAdminScript() . '?target=main');

        } elseif (static::isSafeModeRequested() && !static::isSafeModeStarted()) {

            $restorePoint = null;
            if (static::isRestoreDateSet()) {
                $restorePoint = \Includes\Utils\ModulesManager::getRestorePoint(static::getRestoreDate());
            }

            if (static::isSoftwareResetEnabled()) {
                if (!($restorePoint != null ^ static::isRestoreDateSet())) {
                    // Put safe mode indicator
                    \Includes\Utils\FileManager::write(static::getIndicatorFileName(), static::getIndicatorFileContent());

                    // Clean cache indicators to force cache generation
                    \Includes\Decorator\Utils\CacheManager::cleanupCacheIndicators();
                } else {
                    $date = \DateTime::createFromFormat(\Includes\Utils\ModulesManager::RESTORE_DATE_FORMAT, static::getRestoreDate());
                    \Includes\Decorator\Utils\PersistentInfo::set('restoreFailed', $date->getTimestamp());
                }
            }

            // Redirect to avoid loop
            \Includes\Utils\Operator::redirect(\XLite::getAdminScript() . '?target=main');
        }
    }


    /**
     * Check Access Key
     *
     * @return boolean
     */
    protected static function checkAccessKey()
    {
        return !empty($_GET[self::PARAM_ACCESS_KEY]) && static::getAccessKey() === $_GET[self::PARAM_ACCESS_KEY];
    }

    /**
     * Get safe mode indicator file name
     *
     * @return string
     */
    protected static function getIndicatorFileName()
    {
        return LC_DIR_VAR . '.safeModeStarted';
    }

    /**
     * Get safe mode access key file name
     *
     * @return string
     */
    protected static function getAccessKeyFileName()
    {
        return LC_DIR_DATA . '.safeModeAccessKey';
    }

    /**
     * Generate Access Key
     *
     * @return string
     */
    protected static function generateAccessKey()
    {
        return function_exists('openssl_random_pseudo_bytes')
            ? bin2hex(openssl_random_pseudo_bytes(16))
            : md5(microtime(true) + rand(0, 1000000));
    }

    /**
     * Data to write into the indicator file
     *
     * @return string
     */
    protected static function getIndicatorFileContent()
    {
        $data = date('r');
        if (isset($_GET[self::PARAM_RESTORE_DATE])) {
            $data = date('r') . ', ' . self::LABEL_RESTORE_DATE . $_GET[self::PARAM_RESTORE_DATE];
        } elseif (isset($_GET[self::PARAM_SOFT_RESET])) {
            $data = date('r') . ', ' . self::LABEL_SOFT_RESET;
        }
        return $data;
    }


    // {{{ Unsafe modules methods

    /**
     * Remove file with active modules list
     *
     * @return void
     */
    public static function clearUnsafeModules()
    {
        \Includes\Utils\FileManager::deleteFile(static::getUnsafeModulesFilePath());
    }

    /**
     * Get unsafe modules list
     *
     * @param boolean $asPlainList Flag OPTIONAL
     *
     * @return array
     */
    public static function getUnsafeModulesList($asPlainList = true)
    {
        $list = array();
        $path = static::getUnsafeModulesFilePath();

        if (\Includes\Utils\FileManager::isFileReadable($path)) {
            foreach (parse_ini_file($path, true) as $author => $names) {
                foreach (array_filter($names) as $name => $flag) {

                    if ($asPlainList) {
                        $list[] = $author . '\\' . $name;

                    } else {
                        if (!isset($list[$author])) {
                            $list[$author] = array();
                        }

                        $list[$author][$name] = 1;
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Mark module as unsafe
     *
     * @param string $author Module author
     * @param string $name   Module name
     *
     * @return void
     */
    public static function markModuleAsUnsafe($author, $name)
    {
        static::markModulesAsUnsafe(array($author => array($name => true)));
    }

    /**
     * Mark modules as unsafe
     *
     * @param array $modules Modules
     *
     * @return void
     */
    public static function markModulesAsUnsafe(array $modules)
    {
        $list = static::getUnsafeModulesList(false);

        foreach ($modules as $author => $names) {
            foreach ($names as $name => $key) {

                if (!isset($list[$author])) {
                    $list[$author] = array();
                }

                $list[$author][$name] = 1;
            }
        }

        static::saveUnsafeModulesToFile($list);
    }

    /**
     * Get modules list file path
     *
     * @return string
     */
    protected static function getUnsafeModulesFilePath()
    {
        return LC_DIR_VAR . self::UNSAFE_MODULES_FILE_NAME;
    }

    /**
     * Save modules to file
     *
     * @param array $modules Modules array
     *
     * @return integer|boolean
     */
    protected static function saveUnsafeModulesToFile(array $modules)
    {
        $path = static::getUnsafeModulesFilePath();

        $string = '; <' . '?php /*' . PHP_EOL;

        $i = 0;
        foreach ($modules as $author => $names) {
            $string .= '[' . $author . ']' . PHP_EOL;
            foreach ($names as $name => $enabled) {
                $string .= $name . ' = ' . $enabled . PHP_EOL;
                $i++;
            }
        }

        $string .= '; */ ?' . '>';

        return $i ? \Includes\Utils\FileManager::write($path, $string) : false;
    }

    // }}}
}
