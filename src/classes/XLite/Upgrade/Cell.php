<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Upgrade;

/**
 * Cell
 */
class Cell extends \XLite\Base\Singleton
{
    /**
     * Name of TmpVar
     */
    const CELL_NAME = 'upgradeCell';

    /**
     * Dedicated cell entry - XC core
     */
    const CORE_IDENTIFIER = '____CORE____';

    /**
     * Reserve of free disk space (5Mb)
     */
    const FREE_SPACE_RESERVE = 5000000;

    /**
     * Number of packs to process (download or unpack) per request
     */
    const CHUNK_SIZE_DOWNLOAD = 10;
    const CHUNK_SIZE_UNPACK   = 10;

    /**
     * Number of upgrade hooks to run per request
     * Applies only to pre_upgrade and post_rebuild hooks
     */
    const HOOKS_PER_REQUEST   = 10;

    /**
     * List of cell entries
     *
     * @var array
     */
    protected $entries = array();

    /**
     * Core version to upgrade to
     *
     * @var string
     */
    protected $coreVersion;

    /**
     * List of cores received from marketplace (cache)
     *
     * @var array
     */
    protected $coreVersions;

    /**
     * List of incompatible modules
     *
     * @var array
     */
    protected $incompatibleModules = array();

    /**
     * List of disabled modules with hooks
     *
     * @var array
     */
    protected $disabledModulesHooks = array();

    /**
     * List of disabled modules with pre-upgrade hooks
     *
     * @var array
     */
    protected $preUpgradeWarningModules = array();

    /**
     * List of error messages
     *
     * @var array
     */
    protected $errorMessages;

    /**
     * List of entries' names which were failed to download or unpack
     *
     * @var array
     */
    protected $errorEntries = array();

    /**
     * Flag to determine if upgrade is already performed
     *
     * @var boolean
     */
    protected $isUpgraded = false;

    /**
     * List of passed upgrade hooks
     *
     * @var array
     */
    protected $upgradeHooks = array();

    /**
     * Flag: If true then currect iteration of hooks execution must be stopped and need to redirect to the next iteration
     *
     * @var boolean
     */
    protected $hookRedirect = false;

    // {{{ Public methods

    /**
     * Check if cell is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        return ! (
            (bool) array_filter($this->getErrorMessages())
            || (bool) $this->getPremiumLicenseModules()
        );
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * Return list of incompatible modules
     *
     * @param boolean $onlySelected Flag to return only the modules selected by admin OPTIONAL
     *
     * @return array
     */
    public function getIncompatibleModules($onlySelected = false)
    {
        $result = array();

        /** @var \XLite\Model\Module $module */
        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Module')->findByEnabled(true) as $module) {
            $key = $module->getMarketplaceID();

            if (isset($this->incompatibleModules[$key])
                && (!$onlySelected || $this->incompatibleModules[$key])
            ) {
                $result[$key] = $module;
            }
        }

        return $result;
    }

    /**
     * Store the disabled module marketplace ID
     *
     * @param string $marketplaceId Marketplace id
     *
     * @return void
     */
    public function addDisabledModulesHook($marketplaceId)
    {
        $this->disabledModulesHooks[$marketplaceId] = $marketplaceId;
    }

    /**
     * Store the disabled module with pre-upgrade hooks marketplace ID
     *
     * @param string $marketplaceId Marketplace id
     *
     * @return void
     */
    public function addPreUpgradeWarningModules($marketplaceId)
    {
        $this->preUpgradeWarningModules[$marketplaceId] = $marketplaceId;
    }

    /**
     * Return list of disabled modules if the modules have hooks
     *
     * @return array
     */
    public function getDisabledModulesHooks()
    {
        $result = array();

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Module')->findByEnabled(false) as $module) {
            $key = $module->getMarketplaceID();

            if (isset($this->disabledModulesHooks[$key])) {
                $result[$key] = $module;
            }
        }

        return $result;
    }

    /**
     * Return list of disabled modules if the modules have pre_upgrade hooks
     *
     * @return array
     */
    public function getPreUpgradeWarningModules()
    {
        $result = array();

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Module')->findByEnabled(false) as $module) {
            $key = $module->getMarketplaceID();

            if (isset($this->preUpgradeWarningModules[$key])) {
                $result[$key] = $module;
            }
        }

        return $result;
    }

    /**
     * Set the list of disabled modules with the pre_upgrade hooks
     *
     * @param array $modules Modules
     *
     * @return void
     */
    public function setPreUpgradeWarningModules($modules)
    {
        foreach ($this->preUpgradeWarningModules as $marketplaceID => $value) {
            if (!in_array($marketplaceID, $modules)) {
                unset($this->preUpgradeWarningModules[$marketplaceID]);
            }
        }
    }

    /**
     * Set statuses (enable/disable) for incompatible modules
     *
     * @param array $statuses List of statuses (<moduleID,status>)
     *
     * @return void
     */
    public function setIncompatibleModuleStatuses(array $statuses)
    {
        $this->incompatibleModules = array_intersect_key($statuses, $this->incompatibleModules)
            + $this->incompatibleModules;
    }

    /**
     * Return list of custom files
     *
     * @return array
     */
    public function getCustomFiles()
    {
        return array_merge(
            \Includes\Utils\ArrayManager::getObjectsArrayFieldValues($this->getEntries(), 'getCustomFiles')
        );
    }

    /**
     * Return list of premium license modules
     *
     * @return array
     */
    public function getPremiumLicenseModules()
    {
        $result = array();
        foreach ($this->getEntries() as $entry) {
            $result = array_merge($result, $entry->getPremiumLicenseModules());
        }

        return $result;
    }

    /**
     * Method to clean up cell
     *
     * @param boolean $clearCoreVersion Flag OPTIONAL
     * @param boolean $clearEntries     Flag OPTIONAL
     * @param boolean $collectEntries   Flag OPTIONAL
     *
     * @return void
     */
    public function clear($clearCoreVersion = true, $clearEntries = true, $collectEntries = true)
    {
        foreach ($this->getEntries() as $entry) {
            $entry->clear();
        }

        $this->incompatibleModules = array();
        $this->setUpgraded(false);

        if ($clearCoreVersion) {
            $this->setCoreVersion(null);
        }

        if ($clearEntries) {
            $this->entries = array();
        }

        if ($collectEntries) {
            $this->collectEntries();
        }

        $this->errorMessages = null;
    }

    /**
     * Define version of core to upgrade to
     *
     * @param string $version Version to set
     *
     * @return void
     */
    public function setCoreVersion($version)
    {
        $this->coreVersion = $version;
    }

    /**
     * Set cell status
     *
     * @param boolean $value Flag
     *
     * @return void
     */
    public function setUpgraded($value)
    {
        $this->isUpgraded = (bool) $value;

        if ($this->isUpgraded) {
            foreach ($this->getEntries() as $entry) {
                $entry->setUpgraded();
            }
        }
    }

    /**
     * Add module to update/install
     *
     * @param \XLite\Model\Module $module Module model
     * @param boolean             $force  Flag to install modules OPTIONAL
     *
     * @return \XLite\Upgrade\Entry\Module\Marketplace
     */
    public function addMarketplaceModule(\XLite\Model\Module $module, $force = false)
    {
        if ($force) {
            $toUpgrade = $module;

        } else {
            $repo = \XLite\Core\Database::getRepo('\XLite\Model\Module');
            $majorVersion = $this->getCoreMajorVersion();

            // "ForUpgrade" or "ForUpdate" method call

            $toUpgrade = $repo->getModuleForUpgrade($module);

            if (!$toUpgrade || $toUpgrade->getMajorVersion() != $majorVersion) {
                $toUpgrade = $repo->getModuleForUpdate($module);

                if ($toUpgrade && $toUpgrade->getMajorVersion() != $majorVersion) {
                    $toUpgrade = null;
                }
            }
        }

        $hash = $module->getActualName();

        $result = null;
        if ($toUpgrade) {
            $result = $this->addEntry($hash, 'Module\Marketplace', array($module, $toUpgrade));

        } elseif ($module->getEnabled()) {
            $this->incompatibleModules[$module->getMarketplaceID()] = false;
        }

        return $result;
    }

    /**
     * Add module to update/install
     *
     * @param string $path Path to uploaded module pack
     *
     * @return \XLite\Upgrade\Entry\Module\Uploaded
     */
    public function addUploadedModule($path)
    {
        return $this->addEntry(md5($path), 'Module\Uploaded', array($path));
    }

    /**
     * Get list of available upgrade hooks
     *
     * @return array
     */
    public function getUpgradeHooks()
    {
        return $this->upgradeHooks;
    }

    /**
     * Get list of pending upgrade hooks
     *
     * @return array
     */
    public function getPendingHooks()
    {
        return $this->upgradeHooks
            ? array_filter(
                $this->upgradeHooks,
                function ($value) {
                    return null !== $value;
                }
            )
            : array();
    }

    /**
     * Get list of invoked upgrade hooks
     *
     * @return array
     */
    public function getInvokedHooks()
    {
        return $this->upgradeHooks
            ? array_filter(
                $this->upgradeHooks,
                function ($value) {
                    return null === $value;
                }
            )
            : array();
    }

    /**
     * Add passed hook
     *
     * @param string $file   Hook file name
     * @param mixed  $status Status of passed upgrade hook
     *
     * @return void
     */
    public function addPassedHook($file, $status)
    {
        $this->upgradeHooks[$file] = $status;
    }

    /**
     * Return true if there are unfinished upgrade hooks
     *
     * @param string                      $type  Type of hook
     * @param \XLite\Upgrade\Entry\AEntry $entry Entry
     *
     * @return boolean
     */
    public function hasUnfinishedUpgradeHooks($type, $entry = null)
    {
        $result = false;

        if ($this->upgradeHooks) {

            $pattern = '';

            if ($entry) {
                $ds = preg_quote(LC_DS, '/');
                $prefix = 'Core' === $entry->getActualName()
                    ? '^(' . $ds . '?)'
                    : preg_quote(str_replace('\\', LC_DS, $entry->getActualName()) . LC_DS, '/');

                $pattern = $prefix . 'upgrade' . $ds . '.+';
            }

            $pattern = '/' . $pattern . preg_quote($type, '/') . '\.php$/';

            foreach ($this->getPendingHooks() as $file => $status) {
                if (preg_match($pattern, $file)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    // }}}

    // {{{ Core version routines

    /**
     * Quick access to the "Core" entry
     *
     * @return string
     */
    public function getCoreMajorVersion()
    {
        return $this->callCoreEntryMethod('getMajorVersionNew') ?: \XLite::getInstance()->getMajorVersion();
    }

    /**
     * Quick access to the "Core" entry
     *
     * @return string
     */
    public function getCoreMinorVersion()
    {
        return $this->callCoreEntryMethod('getMinorVersionNew') ?: \XLite::getInstance()->getMinorVersion();
    }

    /**
     * Quick access to the "Core" entry
     *
     * @return string
     */
    public function getCoreVersion()
    {
        return $this->callCoreEntryMethod('getVersionNew') ?: \XLite::getInstance()->getVersion();
    }

    /**
     * Get list of available kernel versions from the marketplace
     *
     * @return array
     */
    public function getCoreVersions()
    {
        if (!isset($this->coreVersions)) {
            $this->coreVersions = (array) \XLite\Core\Marketplace::getInstance()->getCores($this->getCacheTTL());
        }

        return $this->coreVersions;
    }

    /**
     * Check if we upgrade core major version
     *
     * @return boolean
     */
    public function isUpgrade()
    {
        return \XLite::getInstance()->checkVersion($this->getCoreMajorVersion(), '<');
    }

    /**
     * Remove the specific module entry
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return void
     */
    public function removeModuleEntry(\XLite\Model\Module $module)
    {
        $hash = $module->getActualName();
        if (isset($this->entries[$hash])) {
            unset($this->entries[$hash]);
        }
    }

    /**
     * Helper
     *
     * @param string $method Name of method to call
     *
     * @return mixed
     */
    protected function callCoreEntryMethod($method)
    {
        $entry = \Includes\Utils\ArrayManager::getIndex($this->getEntries(), self::CORE_IDENTIFIER, true);

        // If core entry found, call method with the passed name on it
        return isset($entry) ? $entry->$method() : null;
    }

    // }}}

    // {{{ "Magic" methods

    /**
     * Save data in DB
     *
     * @return void
     */
    public function __destruct()
    {
        // WARNING! Do not change the order of the structure data.
        // The NEW information must be added to the TAIL of the structure strictly!
        \XLite\Core\TmpVars::getInstance()->{self::CELL_NAME} = $this->getEntries()
            ? array(
                $this->getEntries(),
                $this->isUpgraded(),
                $this->disabledModulesHooks,
                $this->incompatibleModules,
                $this->preUpgradeWarningModules,
                $this->upgradeHooks,
            )
            : null;
    }

    /**
     * Protected constructor
     *
     * @return void
     */
    protected function __construct()
    {
        parent::__construct();

        // Upload addons info into the database
        \XLite\Core\Marketplace::getInstance()->getAddonsList($this->getCacheTTL());

        $coreVersionBeforeUpgrade = \XLite\Core\Config::getInstance()->Internal->coreVersionBeforeUpgrade;

        // WARNING! Do not change the order of the structure data.
        // The NEW information must be added to the TAIL of the structure strictly!
        list(
            $entries,
            $isUpgraded,
            $disabledModulesHooks,
            $incompatibleModules,
            $preUpgradeWarningModules,
            $upgradeHooks
        ) = \XLite\Core\TmpVars::getInstance()->{self::CELL_NAME};

        // Hack for the 5.1.2 and previous versions.
        // @see #BUG-537 for more details
        if ($coreVersionBeforeUpgrade && version_compare($coreVersionBeforeUpgrade, '5.1.2', '<=')) {
            list(
                $entries,
                $incompatibleModules,
                $isUpgraded,
                $disabledModulesHooks,
                $preUpgradeWarningModules,
                $upgradeHooks
            ) = \XLite\Core\TmpVars::getInstance()->{self::CELL_NAME};
        }

        if (is_array($entries)) {
            $this->entries = array_merge($this->entries, $entries);
            $this->incompatibleModules = $this->incompatibleModules + (array) $incompatibleModules;
            $this->disabledModulesHooks = $this->disabledModulesHooks + (array) $disabledModulesHooks;
            $this->preUpgradeWarningModules = $this->preUpgradeWarningModules + (array) $preUpgradeWarningModules;
            $this->upgradeHooks = $upgradeHooks ?: array();

            $this->setUpgraded(!empty($isUpgraded));

        } else {
            $this->collectEntries();
        }
    }

    /**
     * Initialize upgrade hooks list
     *
     * @return void
     */
    public function initUpgradeHooks()
    {
        $upgradeHooks = array_flip($this->preloadHelpers());
        array_walk(
            $upgradeHooks,
            function (&$v, $k) {
                return $v = -1;
            }
        );
        $this->upgradeHooks = $upgradeHooks;
    }

    /**
     * Return so called "short" TTL
     *
     * @return integer
     */
    protected function getCacheTTL()
    {
        return \XLite\Core\Marketplace::TTL_SHORT;
    }

    // }}}

    // {{{ Methods to collect entries

    /**
     * Check and add (if needed) upgrade entries
     *
     * @return void
     */
    protected function collectEntries()
    {
        if (!$this->isUpgraded()) {
            // :NOTE: do not change call order!
            $this->checkForCoreUpgrade();
            $this->checkForModulesUpgrade();
        }
    }

    /**
     * Check and add (if needed) core upgrade entry
     *
     * @return \XLite\Upgrade\Entry\Core
     */
    protected function checkForCoreUpgrade()
    {
        $majorVersion = $this->coreVersion ?: \XLite::getInstance()->getMajorVersion();
        $data = \Includes\Utils\ArrayManager::getIndex($this->getCoreVersions(), $majorVersion, true);

        $result = null;
        if (is_array($data) && $this->isCoreUpgradeSelected()) {
            $result = $this->addEntry(self::CORE_IDENTIFIER, 'Core', array_merge(array($majorVersion), $data));
            $this->setCoreVersion($majorVersion);
        }

        return $result;
    }

    /**
     * Check and add (if needed) upgrade entries
     *
     * @return void
     */
    protected function checkForModulesUpgrade()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Module::P_INSTALLED} = true;

        foreach (\XLite\Core\Database::getRepo('\XLite\Model\Module')->search($cnd) as $module) {
            if ($this->isModuleUpgradeSelected($module)) {
                $this->addMarketplaceModule($module);
            }
        }
    }

    /**
     * Return true if core upgrade is allowed (selected by user)
     *
     * @return boolean
     */
    protected function isCoreUpgradeSelected()
    {
        $result = true;

        if (\XLite\Core\Session::getInstance()->selectedEntries
            && is_array(\XLite\Core\Session::getInstance()->selectedEntries)
            && empty(\XLite\Core\Session::getInstance()->selectedEntries['core'])
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Return true if module upgrade is allowed (selected by user)
     *
     * @param \XLite\Model\Module $module Module object
     *
     * @return boolean
     */
    protected function isModuleUpgradeSelected($module)
    {
        $result = true;

        if (\XLite\Core\Session::getInstance()->selectedEntries
            && is_array(\XLite\Core\Session::getInstance()->selectedEntries)
        ) {
            $moduleID = $module->getMarketplaceID();
            $result = !empty(\XLite\Core\Session::getInstance()->selectedEntries[$moduleID]);
        }

        if (!$result && $module->getIsSystem() && $this->isCoreUpgradeSelected()) {
            $result = true;
        }

        return $result;
    }

    /**
     * Common method to add entries
     *
     * @param string $index Index in the "entries" array
     * @param string $class Entry class name
     * @param array  $args  Constructor arguments OPTIONAL
     *
     * @return \XLite\Upgrade\Entry\AEntry
     */
    protected function addEntry($index, $class, array $args = array())
    {
        try {
            $entry = \Includes\Pattern\Factory::create('\XLite\Upgrade\Entry\\' . $class, $args);

        } catch (\Exception $exception) {
            $entry = null;
            \XLite\Upgrade\Logger::getInstance()->logError($exception->getMessage());
        }

        if (isset($entry)) {
            $this->entries[$index] = $entry;
        }

        return $entry;
    }

    // }}}

    // {{{ Errors handling

    /**
     * Return list of error messages
     *
     * @return array
     */
    public function getErrorMessages()
    {
        if (!isset($this->errorMessages)) {
            $this->errorMessages = array();

            $freeSpaceError = $this->isFreeSpaceCheckAvailable()
                ? $this->checkDiskFreeSpace()
                : false;

            if (!$this->isUnpacked() && $freeSpaceError) {
                $this->errorMessages[self::CORE_IDENTIFIER] = array($freeSpaceError);
            }

            $this->errorMessages = array_merge(
                $this->errorMessages,
                \Includes\Utils\ArrayManager::getObjectsArrayFieldValues($this->getEntries(), 'getErrorMessages')
            );

            $this->errorMessages = array_filter($this->errorMessages);
        }

        return $this->errorMessages;
    }

    /**
     * Return list of error entries
     *
     * @return array
     */
    public function getErrorEntries()
    {
        return $this->errorEntries;
    }

    /**
     * Is disk_free_space function available
     * 
     * @return boolean
     */
    public function isFreeSpaceCheckAvailable()
    {
        return null !== \Includes\Utils\FileManager::getDiskFreeSpace(LC_DIR_TMP);
    }

    /**
     * Check if there is enough disk free space.
     * Return message on error
     *
     * @return string
     */
    protected function checkDiskFreeSpace()
    {
        $message = null;

        $totalSize = \Includes\Utils\ArrayManager::sumObjectsArrayFieldValues($this->getEntries(), 'getPackSize');
        $freeSpaceRaw = \Includes\Utils\FileManager::getDiskFreeSpace(LC_DIR_TMP);
        $freeSpace = null !== $freeSpaceRaw
            ? max(0, $freeSpaceRaw - self::FREE_SPACE_RESERVE)
            : null;

        if (null !== $freeSpace && $totalSize > $freeSpace) {
            $message = \XLite\Core\Translation::getInstance()->translate(
                'Not enough disk space. Required: {{req}} (+{{reserve}} reserve). Available: {{avail}}',
                array(
                    'req'     => \XLite\Core\Converter::formatFileSize($totalSize),
                    'reserve' => \XLite\Core\Converter::formatFileSize(self::FREE_SPACE_RESERVE),
                    'avail'   => \XLite\Core\Converter::formatFileSize($freeSpace),
                )
            );
        }

        return $message;
    }

    // }}}

    // {{{ Check cell status

    /**
     * Check if all entry packages were downloaded
     *
     * @return boolean
     */
    public function isDownloaded()
    {
        return $this->checkCellPackages(false);
    }

    /**
     * Check if all entry packages were unpacked
     *
     * @return boolean
     */
    public function isUnpacked()
    {
        return $this->checkCellPackages(true);
    }

    /**
     * Check if upgrade is already performed
     *
     * @return boolean
     */
    public function isUpgraded()
    {
        return $this->isUpgraded;
    }

    /**
     * Common method to check entry packages
     *
     * @param boolean $isUnpacked Check type
     *
     * @return boolean
     */
    protected function checkCellPackages($isUnpacked)
    {
        $result = false;

        $list  = $this->getEntries();
        $count = count($list);

        if (0 < $count) {
            $result = true;

            foreach ($list as $entry) {
                if (!$entry->{$isUnpacked ? 'isUnpacked' : 'isDownloaded'}()) {
                    $this->errorEntries[] = $entry->getName();
                    $result = false;
                }
            }
        }

        return $result;
    }

    // }}}

    // {{{ Download and unpack archives

    /**
     * Download all update packs
     *
     * @return boolean
     */
    public function downloadUpgradePacks()
    {
        return $this->manageEntryPackages(false);
    }

    /**
     * Unpack all archives
     *
     * @return boolean
     */
    public function unpackAll()
    {
        $result = false;

        if (!$this->isDownloaded()) {
            \XLite\Upgrade\Logger::getInstance()->logError('Trying to unpack non-downloaded archives');

        } else {
            $result = $this->manageEntryPackages(true);
        }

        return $result;
    }

    /**
     * Common method to manage entry packages
     *
     * @param boolean $isUnpack Operation type
     *
     * @return boolean
     */
    protected function manageEntryPackages($isUnpack)
    {
        $cntProcessed = 0;
        $cntTotal = 0;
        $success = true;

        $entries = $this->getEntries();

        foreach ($entries as $entry) {

            $cntTotal++;
            $skip = $isUnpack ? $entry->isUnpacked() : $entry->isDownloaded();

            if (!$skip) {

                if ($isUnpack) {
                    $entry->unpack();
                    $success = $entry->isUnpacked();
                    \Includes\Utils\Operator::showMessage(
                        \XLite\Core\Translation::getInstance()->translate(
                            '...Unpacking {{entry}}...{{status}}',
                            array('entry' => addslashes($entry->getActualName()), 'status' => $success ? 'OK' : 'Failed')
                        )
                    );

                } else {
                    $entry->download();
                    $success = $entry->isDownloaded();
                    \Includes\Utils\Operator::showMessage(
                        \XLite\Core\Translation::getInstance()->translate(
                            '...Downloading {{entry}}...{{status}}',
                            array('entry' => addslashes($entry->getActualName()), 'status' => $success ? 'OK' : 'Failed')
                        )
                    );

                }

                if ($success) {
                    $cntProcessed++;
                    if ($this->isEntryOperationLimitReached($cntProcessed, $isUnpack)) {
                        break;
                    }

                } else {
                    $this->errorEntries[] = $entry->getName();
                    break;
                }
            }
        }

        return $success && (count($entries) > $cntTotal || ($isUnpack ? $this->isUnpacked() : $this->isDownloaded()));
    }

    /**
     * Return true if $i greater or equal to the limit of specific operation (download or unpack)
     *
     * @param integer $i        Counter value
     * @param boolean $isUnpack Operation type
     *
     * @return boolean
     */
    protected function isEntryOperationLimitReached($i, $isUnpack)
    {
        return ($isUnpack ? static::CHUNK_SIZE_UNPACK : static::CHUNK_SIZE_DOWNLOAD) <= $i;
    }

    // }}}

    /**
     * Run pre-upgrade hooks
     *
     * @return boolean
     */
    public function preUpgradeHooks()
    {
        $result = false;

        if (!$this->isUnpacked()) {
            \XLite\Upgrade\Logger::getInstance()->logError(
                'Trying to run pre-upgrade hooks while not all archives were unpacked'
            );

        } else {
            $this->runHelpers('pre_upgrade');
            $result = $this->isValid();
        }
    }

    // {{{ Upgrade

    /**
     * Perform upgrade
     *
     * @param boolean $isTestMode       Flag OPTIONAL
     * @param array   $filesToOverwrite List of custom files to overwrite OPTIONAL
     *
     * @return boolean
     */
    public function upgrade($isTestMode = true, array $filesToOverwrite = array())
    {
        $result = false;

        if (!$this->isUnpacked()) {
            \XLite\Upgrade\Logger::getInstance()->logError(
                'Trying to perform upgrade while not all archives were unpacked'
            );

        } else {

            if (!$isTestMode) {
                $this->preloadLibraries();
                $this->preloadHelpers();
            }

            foreach ($this->getEntries() as $entry) {
                $entry->upgrade($isTestMode, $filesToOverwrite);
            }

            $this->runHelpers('post_upgrade', $isTestMode);
            $result = $this->isValid();
        }

        return $result;
    }

    /**
     * Preload helpers
     *
     * @return array
     */
    public function preloadHelpers()
    {
        $list = array();

        foreach ($this->getEntries() as $entry) {
            $list = array_merge($list, $entry->getHelpers());
        }

        return $list;
    }

    /**
     * Preload libraries
     *
     * @return void
     */
    protected function preloadLibraries()
    {
        // Preload lib directory
        $dirIterator = new \RecursiveDirectoryIterator(LC_DIR_LIB);
        $iterator    = new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::CHILD_FIRST);

        $logLibDir  = LC_DIR_LIB . 'Log' . LC_DS;
        $pearFile   = LC_DIR_LIB . 'PEAR';
        $purifier   = LC_DIR_LIB . 'htmlpurifier';
        $dompdf     = LC_DIR_LIB . 'dompdf';

        // Exclude specific Symfony component
        $symfonyComponent = LC_DIR_LIB . 'Symfony' . LC_DS . 'Component' . LC_DS . 'EventDispatcher';

        $doctrineDir = defined('LC_CACHE_LOADED')
            ? LC_DIR_LIB . 'Doctrine' . LC_DS
            : null;

        foreach ($iterator as $filePath => $fileObject) {
            if (preg_match('/\.php$/Ss', $filePath)
                && (false === stristr($filePath, $logLibDir))
                && (false === stristr($filePath, $pearFile))
                && (false === stristr($filePath, $symfonyComponent))
                && (false === stristr($filePath, $purifier))
                && (false === stristr($filePath, $dompdf))
                && (!$doctrineDir || false === stristr($filePath, $doctrineDir))
            ) {
                require_once $filePath;
            }
        }

        // Preload \Includes
        if (!defined('LC_CACHE_LOADED')) {
            $dirIterator = new \RecursiveDirectoryIterator(LC_DIR_INCLUDES);
            $iterator    = new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($iterator as $filePath => $fileObject) {
                if (preg_match('/\.php$/Ss', $filePath) && !preg_match('/install/Ss', $filePath)) {
                    require_once $filePath;
                }
            }
        }
    }

    // }}}

    // {{{ So called upgrade helpers

    /**
     * Execute some methods
     * pre_upgrade and post_rebuild hooks may be iterative
     * post_upgrade hooks all executed in single thread
     *
     * @param string  $type       Helper type
     * @param boolean $isTestMode Flag OPTIONAL
     *
     * @return void
     */
    public function runHelpers($type, $isTestMode = false)
    {
        if (!$isTestMode) {

            $cnt = 0;

            foreach ($this->getEntries() as $entry) {

                if ($this->runHelper($entry, $type, $isTestMode)) {

                    // Hook has been invoked, increase counter
                    $cnt++;

                    if ('post_upgrade' != $type && ($this->hookRedirect || static::HOOKS_PER_REQUEST <= $cnt)) {
                        break;
                    }
                }
            }
        }
    }

    /**
     * Get hookRedirect property value
     *
     * @return boolean
     */
    public function getHookRedirect()
    {
        return $this->hookRedirect;
    }

    /**
     * Set hookRedirect property
     *
     * @param boolean $value Value
     *
     * @return void
     */
    public function setHookRedirect($value)
    {
        $this->hookRedirect = $value;
    }

    /**
     * Execute some methods
     *
     * @param \XLite\Upgrade\Entry\AEntry $entry      Entry
     * @param string                      $type       Helper type
     * @param boolean                     $isTestMode Flag OPTIONAL
     *
     * @return boolean
     */
    public function runHelper($entry, $type, $isTestMode = false)
    {
        $result = false;

        if (!$isTestMode) {
            // We run pre_upgrade helper only if module is not in the pre-upgrade warning modules list
            // (admin can switch off pre-upgrade hooks for upgraded modules in interface)
            // post_upgrade and post_rebuild hooks do not have any restrictions
            if (!('pre_upgrade' === $type
                && isset($this->preUpgradeWarningModules[$entry->getMarketplaceID()])
                )
            ) {
                $result = $entry->runHelpers($type);
            }
        }

        return $result;
    }

    /**
     * Execute some methods
     *
     * @param string  $type       Helper type
     * @param boolean $isTestMode Flag OPTIONAL
     *
     * @return void
     */
    public function runCommonHelpers($type, $isTestMode = false)
    {
        if (!$isTestMode) {
            foreach ($this->getEntries() as $entry) {
                $this->runCommonHelper($entry, $type, $isTestMode);
            }
        }
    }

    /**
     * Execute some methods
     *
     * @param \XLite\Upgrade\Entry\AEntry $entry      Entry
     * @param string                      $type       Helper type
     * @param boolean                     $isTestMode Flag OPTIONAL
     *
     * @return void
     */
    public function runCommonHelper($entry, $type, $isTestMode = false)
    {
        if (!$isTestMode) {
            $entry->runCommonHelpers($type);
        }
    }

    /**
     * Call install events
     *
     * @param boolean $isTestMode Flag OPTIONAL
     *
     * @return void
     */
    public function callInstallEvents($isTestMode = false)
    {
        if (!$isTestMode) {
            foreach ($this->getEntries() as $entry) {
                $this->callInstallEvent($entry, $isTestMode);
            }
        }
    }

    /**
     * Call install event
     *
     * @param \XLite\Upgrade\Entry\AEntry $entry      Entry
     * @param boolean                     $isTestMode Flag OPTIONAL
     *
     * @return void
     */
    public function callInstallEvent($entry, $isTestMode = false)
    {
        if (!$isTestMode) {
            $entry->callInstallEvent();
        }
    }

    // }}}

    /**
     * Check if there is a core update in the update entries.
     *
     * @return boolean
     */
    public function hasCoreUpdate()
    {
        $result = false;

        foreach ($this->getEntries() as $entry) {
            if ('XLite\Upgrade\Entry\Core' === get_class($entry)) {
                $result = true;
            }
        }

        return $result;
    }
}
