<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Upgrade
 */
class Upgrade extends \XLite\Controller\Admin\Base\Addon
{
    /**
     * List of expired keys
     *
     * @var array
     */
    protected $expiredKeys = null;

    /**
     * URL to purchase all expired keys prolongations at once
     *
     * @var string
     */
    protected $allKeysPurchaseURL = null;

    /**
     * Flag: Are there expired license keys which will block upgrade routine
     *
     * @var boolean
     */
    protected $hasBlockedExpiredKeys = false;

    /**
     * Errors of requirements validation
     *
     * @var array
     */
    protected $requirementErrors;

    // {{{ Common methods

    /**
     * Run controller
     *
     * @return void
     */
    protected function run()
    {
        // Clear marketplace server error flag
        \XLite\Core\Session::getInstance()->mpServerError = null;

        // Clear all selection if you visit the "Available updates" page
        if ($this->isUpdate()) {
            if (!$this->isUpdateModeSelectorAvailable()) {
                \XLite\Core\Session::getInstance()->upgradeHotfixMode = null;
            }
            \XLite\Core\Session::getInstance()->selectedEntries = null;
            \XLite\Upgrade\Cell::getInstance()->clear();
        }

        if (\XLite\Upgrade\Cell::getInstance()->isUpgraded()) {
            if ($this->isForce()) {
                // Module is installed - redirect to the installed modules list

                /*
                \XLite\Upgrade\Cell::getInstance()->runHelpers('post_rebuild');
                \XLite\Upgrade\Cell::getInstance()->runCommonHelpers('add_labels');

                \XLite\Upgrade\Cell::getInstance()->callInstallEvents();
                */

                if ($this->getPaymentMethodToInstall()) {
                    $url = $this->getPaymentSettingsPageURL();
                    \XLite\Core\Session::getInstance()->paymentMethodToInstall = null;
                }

                if (empty($url)) {
                    $url = $this->buildURL(
                        \XLite\Core\Request::getInstance()->redirect ?: 'addons_list_installed',
                        '',
                        array('recent' => true)
                    );
                }

                $this->setReturnURL($url);
                \XLite\Core\Marketplace::getInstance()->clearActionCache();

            } else {
                // Upgrade is completed

                $skipPostUpgradeAction = false;

                if (\XLite\Core\Session::getInstance()->flagIsUpgraded) {
                    \XLite\Upgrade\Cell::getInstance()->clear();
                    \XLite\Core\Session::getInstance()->flagIsUpgraded = null;
                    $skipPostUpgradeAction = true;
                } else {
                    \XLite\Core\Session::getInstance()->flagIsUpgraded = true;
                }

                if (!$skipPostUpgradeAction) {
                    // post_rebuild hooks running
                    /*
                    \XLite\Upgrade\Cell::getInstance()->runHelpers('post_rebuild');
                    \XLite\Upgrade\Cell::getInstance()->runCommonHelpers('add_labels');

                    \XLite\Upgrade\Cell::getInstance()->callInstallEvents();
                    */

                    \XLite\Core\Marketplace::getInstance()->clearActionCache();
                }
            }
        } else {
            \XLite\Core\Session::getInstance()->flagIsUpgraded = null;
        }

        parent::run();
    }

    /**
     * Return true if selector 'bug-fixes only / new features and bug-fixes' is available
     *
     * @return boolean
     */
    public function isUpdateModeSelectorAvailable()
    {
        return $this->isUpdate() && \XLite\Upgrade\Cell::getInstance()->isUpgradeHotfixModeSelectorAvailable();
    }

    // }}}

    // {{{ Methods for viewers

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        if ($this->isDownload()) {
            $result = static::t('Downloading updates');

        } else {
            $version = \XLite\Upgrade\Cell::getInstance()->getCoreMajorVersion();

            if (\XLite::getInstance()->checkVersion($version, '<')) {
                $result = 'Upgrade to version {{version}}';

            } else {
                $result = 'Updates for your version ({{version}})';
            }

            $result = static::t($result, array('version' => $version));
        }

        return $result;
    }

    /**
     * Check if core major version is equal to the current one
     *
     * @return boolean
     */
    public function isUpdate()
    {
        return 'install_updates' === \XLite\Core\Request::getInstance()->mode;
    }

    /**
     * Check if current page is the updates download dialog
     *
     * @return boolean
     */
    public function isDownload()
    {
        return 'download_updates' === \XLite\Core\Request::getInstance()->mode;
    }

    /**
     * Check if next step of upgrade id available
     *
     * @return boolean
     */
    public function isNextStepAvailable()
    {
        $result = \XLite\Upgrade\Cell::getInstance()->isValid()
            && $this->isValidRequirements();

        if ($result) {

            $isHotfixUpdate = \XLite\Upgrade\Cell::getInstance()->isHotfixUpdate();

            if (\XLite\Upgrade\Cell::getInstance()->hasCoreUpdate()) {
                // Has core update: the core license key must be activated in store
                // (for hot fixes updates status of key is ignored)
                $result = $this->isValidXCNLicense(!$isHotfixUpdate);
            }

            // Allow updates if there are no blocked (expired) keys found or update contains only hot fix entries
            $result = $result
                && (
                    $isHotfixUpdate
                    || !$this->hasBlockedExpiredKeys
                );
        }

        return $result;
    }

    /**
     * Is disk_free_space function available
     *
     * @return boolean
     */
    public function isFreeSpaceCheckAvailable()
    {
        return \XLite\Upgrade\Cell::getInstance()->isFreeSpaceCheckAvailable();
    }

    /**
     * Return true if requirements for the upgrade are satisfied
     *
     * @return boolean
     */
    public function isValidRequirements()
    {
        if (!isset($this->requirementErrors)) {

            $this->requirementErrors = array();

            if (
                version_compare(\XLite::getInstance()->getMajorVersion(), '5.2', '=')
                && version_compare(\XLite\Upgrade\Cell::getInstance()->getCoreMajorVersion(), '5.3', '>=')
                && version_compare(PHP_VERSION, '5.4', '<')
            ) { 
                // Upgrade 5.2 -> 5.3: PHP v5.4+ is required
                $this->requirementErrors['php_version'] = static::t('X-Cart v5.3 requires PHP version 5.4 or better. Please update the PHP version installed on your server before you proceed.');
            }
        }

        return empty($this->requirementErrors);
    }

    /**
     * Return list of requirements validation errors
     *
     * @return array
     */
    public function getRequirementErrors()
    {
        return $this->requirementErrors;
    }

    /**
     * Check if the trial notice must be displayed
     *
     * @return boolean
     */
    public function displayTrialNotice()
    {
        return \XLite\Upgrade\Cell::getInstance()->hasCoreUpdate()
            && !\XLite::getXCNLicense();
    }

    /**
     * Return list of all core versions available
     *
     * @return array
     */
    public function getCoreVersionsList()
    {
        $result = \XLite\Upgrade\Cell::getInstance()->getCoreVersions();
        unset($result[\XLite::getInstance()->getMajorVersion()]);

        return $result;
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            array('view_log_file', 'view', 'start_upgrade', 'request_for_upgrade', 'validate_keys', 'toggleHotfixMode')
        );
    }

    /**
     * Check the flag in request
     *
     * @return boolean
     */
    public function isForce()
    {
        return (bool) \XLite\Core\Request::getInstance()->force;
    }

    /**
     * Get some common params for actions
     *
     * @param boolean $force Flag OPTIONAL
     *
     * @return array
     */
    protected function getActionParamsCommon($force = null)
    {
        return ($force ?: $this->isForce()) ? array('force' => true) : array();
    }

    /**
     * Return true if XCN license exists and is valid
     *
     * @param boolean $checkExpDate Flag: true - check license key exp date
     *
     * @return boolean
     */
    protected function isValidXCNLicense($checkExpDate = true)
    {
        $license = \XLite::getXCNLicense();

        if ($license && $checkExpDate) {
            $keyData = $license->getKeyData();
            if (!empty($keyData['message'])) {
                $license = null;
            }
        }

        return (bool) $license;
    }

    // }}}

    // {{{ Action handlers

    /**
     * Install add-ons from marketplace
     *
     * @return void
     */
    protected function doActionInstallAddon()
    {
        \XLite\Upgrade\Cell::getInstance()->clear(true, true, !$this->isForce());
        \XLite\Controller\Admin\AddonsListMarketplace::cleanModulesToInstall();

        foreach (\XLite\Core\Request::getInstance()->moduleIds as $moduleId) {
            $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->find($moduleId);
            if (!$module) {
                $this->showError(
                    __FUNCTION__,
                    static::t('invalid module ID passed: X', array('moduleId' => $moduleId))
                );

            } elseif (!$module->getFromMarketplace()) {
                $this->showError(
                    __FUNCTION__,
                    static::t('trying to install a non-marketplace module: X', array('name' => $module->getActualName()))
                );

            } elseif (!\XLite\Upgrade\Cell::getInstance()->addMarketplaceModule($module, true)) {
                $this->showError(
                    __FUNCTION__,
                    static::t('unable to add module entry to the installation list: X', array('path' => $module->getActualName()))
                );

            } else {
                \XLite\Controller\Admin\Base\AddonsList::storeRecentlyInstalledModules(array($module->getModuleID()));

                if ($this->isForce()) {
                    $this->setHardRedirect(true);
                    $this->setReturnURL($this->buildURL('upgrade', 'download', $this->getActionParamsCommon()));

                } elseif (!$this->isNextStepAvailable() || !$this->isFreeSpaceCheckAvailable()) {
                    $this->setHardRedirect(true);
                    $this->setReturnURL($this->buildURL('upgrade', '', $this->getActionParamsCommon()));

                }
            }
        }
    }

    /**
     * Install uploaded add-on
     *
     * @return void
     */
    protected function doActionUploadAddon()
    {
        $this->setReturnURL($this->buildURL('addons_list_installed'));

        $path = \Includes\Utils\FileManager::moveUploadedFile('modulePack');

        if ($path) {
            \XLite\Upgrade\Cell::getInstance()->clear(true, true, false);
            $entry = \XLite\Upgrade\Cell::getInstance()->addUploadedModule($path);

            if (!isset($entry)) {
                $this->showError(
                    __FUNCTION__,
                    static::t('unable to add module entry to the installation list: X', array('path' => $path))
                );

            } elseif (\XLite::getInstance()->checkVersion($entry->getMajorVersionNew(), '!=')) {
                $this->showError(
                    __FUNCTION__,
                    static::t(
                        'module version X is not equal to the core one (Y)',
                        array(
                            'module_version' => $entry->getMajorVersionNew(),
                            'core_version'   => \XLite::getInstance()->getMajorVersion(),
                        )
                    )
                );

            } elseif ($this->isNextStepAvailable()) {
                $this->setReturnURL($this->buildURL('upgrade', 'download', $this->getActionParamsCommon(true)));

            } else {
                $this->showError(__FUNCTION__);
            }

        } else {
            $this->showError(__FUNCTION__, static::t('unable to upload module'));
        }
    }

    /**
     * Go to the upgrade third step
     *
     * @return void
     */
    protected function doActionDownload()
    {
        $this->setReturnURL($this->buildURL('upgrade'));

        if ($this->isNextStepAvailable()) {
            \Includes\Utils\Operator::showMessage('Downloading updates, please wait...');

            // Disable some modules (if needed)
            \XLite\Upgrade\Cell::getInstance()->setIncompatibleModuleStatuses(
                (array) \XLite\Core\Request::getInstance()->toDisable
            );

            if ($this->runStep('downloadUpgradePacks')) {

                if ($this->isDownloadCompleted()) {
                    $this->setReturnURL($this->buildURL('upgrade', 'unpack', $this->getActionParamsCommon()));

                } else {
                    $this->setReturnURL($this->buildURL('upgrade', 'download', $this->getActionParamsCommon()));
                }

            } else {
                if (!\XLite\Upgrade\Cell::getInstance()->getPremiumLicenseModules()) {
                    $this->showError(__FUNCTION__, static::t('not all upgrade packs were downloaded'));
                    \XLite\Core\Marketplace::getInstance()->checkAddonsKeys(60);
                }
            }

        } else {
            $this->showWarning(__FUNCTION__, static::t('not ready to download packs. Please, try again'));
            $this->setReturnURL(
                $this->buildURL(
                    'addon_install',
                    'warnings',
                    array(
                        'widget' => '\XLite\View\ModulesManager\ModuleWarnings',
                        'moduleIds' => implode(',', \XLite\Core\Request::getInstance()->moduleIds),
                    )
                )
            );
            $this->doRedirect();
        }
    }

    /**
     * Return true if all upgrade packs have been successfully downloaded
     *
     * @return boolean
     */
    protected function isDownloadCompleted()
    {
        return \XLite\Upgrade\Cell::getInstance()->isDownloaded();
    }

    /**
     * Go to the upgrade third step
     *
     * @return void
     */
    protected function doActionUnpack()
    {
        $this->setReturnURL($this->buildURL('upgrade'));

        if (\XLite\Upgrade\Cell::getInstance()->isDownloaded()) {
            \Includes\Utils\Operator::showMessage('Unpacking archives, please wait...');

            if (!$this->runStep('unpackAll')) {
                $this->showError(
                    __FUNCTION__,
                    static::t('not all archives were unpacked', array('list' => $this->getErrorEntriesHTML()))
                );

                \XLite\Core\TopMessage::addError($this->getUnpackErrorMessage());

            } elseif (!\XLite\Upgrade\Cell::getInstance()->isUnpacked()) {
                $this->setReturnURL($this->buildURL('upgrade', 'unpack', $this->getActionParamsCommon()));

            } elseif ($this->isNextStepAvailable()) {
                $this->setReturnURL($this->buildURL('upgrade', 'check_integrity', $this->getActionParamsCommon()));

            } else {
                $this->showError(__FUNCTION__);
            }

        } else {
            $this->showError(__FUNCTION__, static::t('trying to unpack non-downloaded archives'));
        }
    }

    /**
     * Get list of error entries as HTML code
     *
     * @return string
     */
    protected function getErrorEntriesHTML()
    {
        $list = '';

        foreach (\XLite\Upgrade\Cell::getInstance()->getErrorEntries() as $name) {
            $list .= sprintf('<li>%s</li>', $name);
        }

        if ($list) {
            $list = sprintf('<ul class="marked-list">%s</ul>', $list);
        }

        return $list;
    }

    /**
     * Returns error message for 'unpack' error
     *
     * @return string
     */
    protected function getUnpackErrorMessage()
    {
        $link = $this->buildURL('upgrade', 'check_integrity');

        return static::t('Try to unpack them manually', array('link' => $link));
    }

    /**
     * Go to the upgrade third step
     *
     * @return void
     */
    protected function doActionCheckIntegrity()
    {
        $this->setReturnURL($this->buildURL('upgrade', '', $this->getActionParamsCommon()));

        if (\XLite\Upgrade\Cell::getInstance()->isUnpacked()) {
            \Includes\Utils\Operator::showMessage('Checking integrity, please wait...');

            // Perform upgrade in test mode
            $this->runStep('upgrade', array(true));

            if ($this->isForce() && $this->isNextStepAvailable()) {
                $this->setReturnURL($this->buildURL('upgrade', 'install_upgrades', $this->getActionParamsCommon()));
            }

        } else {
            $this->showError(
                __FUNCTION__,
                static::t('unable to test files: not all archives were unpacked', array('list' => $this->getErrorEntriesHTML()))
            );
        }
    }

    /**
     * Third step: install downloaded upgrades
     *
     * @return void
     */
    protected function doActionInstallUpgrades()
    {
        $this->setReturnURL($this->buildURL('upgrade'));

        if (\XLite\Upgrade\Cell::getInstance()->isUnpacked()) {
            if (!$this->isForce()) {
                \Includes\SafeMode::sendNotification();
            }

            $restorePoint = \Includes\Utils\ModulesManager::getEmptyRestorePoint();

            //write current state
            $current = \XLite\Core\Database::getRepo('\XLite\Model\Module')->findBy(array('enabled' => true));
            foreach ($current as $module) {
                $restorePoint['current'][$module->getModuleId()] = $module->getActualName();
            }

            \Includes\Utils\Operator::showMessage('Installing updates, please wait...');

            if (\XLite\Core\Request::getInstance()->preUpgradeWarningModules) {
                \XLite\Upgrade\Cell::getInstance()->setPreUpgradeWarningModules(
                    array_keys(
                        array_filter(
                            \XLite\Core\Request::getInstance()->preUpgradeWarningModules,
                            function($value) {
                                return 0 == $value;
                            }
                        )
                    )
                );
            }

            // Disable selected modules
            $modules = array();
            foreach (\XLite\Upgrade\Cell::getInstance()->getIncompatibleModules(true) as $module) {
                $module->setEnabled(false);
                $modules[] = $module;
                $restorePoint['disabled'][$module->getModuleId()] = $module->getActualName();
            }

            \XLite\Core\Database::getRepo('XLite\Model\Module')->updateInBatch($modules);

            // Do actions according the admin choice for the disabled modules with hooks
            $modulesToEnable = array();

            /** @var \XLite\Model\Module $module */
            foreach (\XLite\Upgrade\Cell::getInstance()->getDisabledModulesHooks() as $marketplaceId => $module) {
                $action = \XLite\Core\Request::getInstance()->disabledModulesHooks[$marketplaceId];
                $installedModule = $module->getModuleInstalled();

                if ($installedModule) {
                    if (1 == $action) {
                        // Enable module
                        $installedModule->setEnabled(true);
                        $modulesToEnable[] = $installedModule;
                        $restorePoint['enabled'][$installedModule->getModuleId()] = $installedModule->getActualName();
                    } elseif (0 == $action) {
                        // Uninstall module
                        \XLite\Upgrade\Cell::getInstance()->removeModuleEntry($installedModule);
                        $this->uninstallModule($installedModule);
                        $restorePoint['deleted'][] = $installedModule->getActualName();
                    }
                }
            }

            \XLite\Core\Database::getRepo('XLite\Model\Module')->updateInBatch($modulesToEnable);

            if (\XLite\Upgrade\Cell::getInstance()->getEntries()) {

                foreach (\XLite\Upgrade\Cell::getInstance()->getEntries() as $module) {
                    $restorePoint['installed'][] = $module->getActualName();
                }

                \XLite\Upgrade\Cell::getInstance()->initUpgradeHooks();

                $this->setReturnURL($this->buildURL('upgrade', 'pre_upgrade_hooks', $this->getActionParamsCommon()));

            } else {
                // All modules for upgrade were set for uninstallation
                // There are no upgrade procedures to perform
                \XLite\Core\Marketplace::getInstance()->clearActionCache();

                $this->setReturnURL($this->buildURL('addons_list_installed'));
            }

            \Includes\Utils\ModulesManager::updateModuleMigrationLog($restorePoint);

        } else {
            $this->showWarning(
                __FUNCTION__,
                static::t('unable to install: not all archives were unpacked. Please, try again', array('list' => $this->getErrorEntriesHTML()))
            );
        }
    }

    /**
     * Fourth step: run pre_upgrade hooks
     *
     * @return void
     */
    protected function doActionPreUpgradeHooks()
    {
        $this->setReturnURL($this->buildURL('upgrade'));

        if (\XLite\Upgrade\Cell::getInstance()->isUnpacked()) {

            \Includes\Utils\Operator::showMessage('Run pre-upgrade hooks, please wait...');

            // Run pre-upgrade hooks
            $this->runStep('preUpgradeHooks');
            if (\XLite\Upgrade\Cell::getInstance()->hasUnfinishedUpgradeHooks('pre_upgrade')) {
                $this->setReturnURL($this->buildURL('upgrade', 'pre_upgrade_hooks', array_merge($this->getActionParamsCommon(), array('_t' => microtime(true)))));

            } else {
                $this->setReturnURL($this->buildURL('upgrade', 'update_files', $this->getActionParamsCommon()));
            }

        } else {
            $this->showWarning(
                __FUNCTION__,
                static::t('Pre-upgrade hooks cannot be run')
            );
        }
    }

    /**
     * Fifth step: copy updated files
     *
     * @return void
     */
    protected function doActionUpdateFiles()
    {
        $this->setReturnURL($this->buildURL('upgrade'));

        if (\XLite\Upgrade\Cell::getInstance()->isUnpacked()) {

            \Includes\Utils\Operator::showMessage('Installing updates, please wait...');

            // Perform upgrade
            // post_upgrade hooks will be proceeded here
            $this->runStep('upgrade', array(false, $this->getFilesToOverWrite()));
            $this->finalizeUpgrade();

        } else {
            $this->showWarning(
                __FUNCTION__,
                static::t('Files cannot be updated')
            );
        }
    }

    /**
     * Final operations after upgrade has been completed
     *
     * @return void
     */
    protected function finalizeUpgrade()
    {
        if ($this->isForce()) {
            if ($this->isNextStepAvailable()) {
                $target = 'installed';
                $entries = \XLite\Upgrade\Cell::getInstance()->getEntries();
                $this->showInfo(
                    null,
                    1 < count($entries)
                    ? static::t('Modules have been successfully installed')
                    : static::t('Module has been successfully installed')
                );

                if ($this->isOnlySkins()) {
                    $target = 'layout';

                } elseif ($this->getPaymentMethodToInstall()) {
                    $target = 'payment_settings';

                } else {
                    $target = 'addons_list_installed';
                }

            } else {
                $target = 'addons_list_marketplace';
                $this->showError(__FUNCTION__);
            }

            $this->setReturnURL(
                $this->buildURL(
                    'upgrade',
                    '',
                    $this->getActionParamsCommon() + array('redirect' => $target)
                )
            );
        }

        // Set cell status
        \XLite\Upgrade\Cell::getInstance()->clear(true, false, false);
        \XLite\Upgrade\Cell::getInstance()->setUpgraded(true);

        // Rebuild cache
        if (!($this->isForce() && $this->isOnlySkins())) {
            \XLite::setCleanUpCacheFlag(true);
        }
    }

    /**
     * Get payment module selected to install from the 'Select payment method' popup
     *
     * @return string
     */
    protected function getPaymentMethodToInstall()
    {
        return \XLite\Core\Session::getInstance()->paymentMethodToInstall;
    }

    /**
     * Get payment method settings page URL
     *
     * @return string
     */
    protected function getPaymentSettingsPageURL()
    {
        $url = null;

        $paymentMethod = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->find($this->getPaymentMethodToInstall());

        if ($paymentMethod) {
            $url = $this->buildURL(
                'payment_settings',
                'add',
                array(
                    'id' => $paymentMethod->getMethodId()
                )
            );
        }

        return $url;
    }

    /**
     * Show log file content
     *
     * @return void
     */
    protected function doActionViewLogFile()
    {
        $path = \XLite\Upgrade\Logger::getInstance()->getLastLogFile();

        if ($path) {
            header('Content-Type: text/plain', true);

            \Includes\Utils\Operator::flush(
                \Includes\Utils\FileManager::read($path)
            );

            exit (0);

        } else {
            \XLite\Core\TopMessage::addWarning('Log files not found');
        }
    }

    /**
     * Install add-on from marketplace
     *
     * @return void
     */
    protected function doActionInstallAddonForce()
    {
        $data = array('moduleIds' => \XLite\Core\Request::getInstance()->moduleIds)
            + $this->getActionParamsCommon(true);
        $this->setReturnURL($this->buildURL('upgrade', 'install_addon', $data));
    }

    /**
     * Start upgrade action
     *
     * @return void
     */
    protected function doActionStartUpgrade()
    {
        $this->setReturnURL($this->buildURL('upgrade'));

        \XLite\Upgrade\Cell::getInstance()->clear();

        if (\XLite\Core\Request::getInstance()->entries) {
            \XLite\Core\Session::getInstance()->selectedEntries = \XLite\Core\Request::getInstance()->entries;
        }

        $version = null;

        $versions = $this->getCoreVersionsList();

        if (is_array($versions)) {
            $versions = array_keys($versions);
            $version = array_shift($versions);
            foreach ($versions as $major) {
                $version = (0 > version_compare($version, $major) ? $version : $major);
            }
        }

        if ($version) {
            \XLite\Upgrade\Cell::getInstance()->setCoreVersion($version);
            \XLite\Upgrade\Cell::getInstance()->clear(false);
            $this->setHardRedirect();
        }
    }

    /**
     * Run an upgrade step
     *
     * @param string $method Upgrade cell method to call
     * @param array  $params Call params OPTIONAL
     *
     * @return mixed
     */
    protected function runStep($method, array $params = array())
    {
        return \Includes\Utils\Operator::executeWithCustomMaxExecTime(
            \Includes\Utils\ConfigParser::getOptions(array('marketplace', 'upgrade_step_time_limit')),
            array(\XLite\Upgrade\Cell::getInstance(), $method),
            $params
        );
    }

    /**
     * Request for upgrade
     *
     * @return void
     */
    protected function doActionRequestForUpgrade()
    {
        $modules = \XLite\Upgrade\Cell::getInstance()->getIncompatibleModules();

        if ($modules) {
            $result = \XLite\Core\Marketplace::getInstance()->requestForUpgrade($modules);

            if (!empty($result)
                && $result[\XLite\Core\Marketplace::FIELD_IS_REQUEST_FOR_UPGRADE_SENT]
            ) {
                \XLite\Core\TopMessage::addInfo('Your request has been sent successfully');

            } else {
                \XLite\Core\TopMessage::addWarning('An error occurred while sending the request');
            }
        }
    }

    /**
     * Action 'Re-validate license keys'
     *
     * @return void
     */
    protected function doActionValidateKeys()
    {
        $result = \XLite\Core\Marketplace::getInstance()->checkAddonsKeys(10);

        $this->setReturnURL($this->buildURL('upgrade'));
    }

    /**
     * Action 'Toggle hotfix mode'
     *
     * @return void
     */
    protected function doActionToggleHotfixMode()
    {
        \XLite\Core\Session::getInstance()->upgradeHotfixMode = !(bool)\XLite\Core\Session::getInstance()->upgradeHotfixMode;

        $this->setReturnURL($this->buildURL('upgrade', null, array('mode' => 'install_updates')));
    }

    // }}}

    // {{{ Some auxiliary methods

    /**
     * Return URL of 'check_integrity' action
     *
     * @return string
     */
    public function getCheckIntegrityURL()
    {
        return $this->buildURL('upgrade', 'check_integrity', $this->getActionParamsCommon());
    }

    /**
     * Retrive list of files that must be overwritten by request for install upgrades
     *
     * @return array
     */
    protected function getFilesToOverWrite()
    {
        $allFilesPlain = array();

        foreach (\XLite\Upgrade\Cell::getInstance()->getCustomFiles() as $files) {
            $allFilesPlain = array_merge($allFilesPlain, $files);
        }

        return \Includes\Utils\ArrayManager::filterByKeys(
            $allFilesPlain,
            array_keys((array) \XLite\Core\Request::getInstance()->toRemain),
            true
        );
    }

    /**
     * Check if module will be disabled after upgrade
     *
     * :TRICKY: check if the "getMajorVersion" is not declared in the main module class
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function isModuleToDisable(\XLite\Model\Module $module)
    {
        $versionCore   = \XLite\Upgrade\Cell::getInstance()->getCoreMajorVersion();
        $versionModule = $module->getMajorVersion();

        $classModule = \Includes\Utils\ModulesManager::getClassNameByModuleName($module->getActualName());
        $reflection  = new \ReflectionMethod($classModule, 'getMajorVersion');

        $classModule = \Includes\Utils\Converter::prepareClassName($classModule);
        $classActual = \Includes\Utils\Converter::prepareClassName($reflection->getDeclaringClass()->getName());

        return version_compare($versionModule, $versionCore, '<') || $classModule !== $classActual;
    }

    /**
     * Check for custom module
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function isModuleCustom(\XLite\Model\Module $module)
    {
        return $module->isCustom();
    }

    /**
     * Get list of expired keys
     *
     * @return array
     */
    public function getExpiredKeys()
    {
        if (!isset($this->expiredKeys)) {
            $this->expiredKeys = array();

            $entries = \XLite\Upgrade\Cell::getInstance()->getEntries();

            $keys = \XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->findAll();

            $commonURLPart = 'https://secure.x-cart.com/customer.php?target=generate_invoice&action=buy&';

            $urlParamsAggregated = array();
            $i = 1;

            foreach ($keys as $key) {
                $entityID = 'CDev' == $key->getAuthor() && 'Core' == $key->getName()
                    ? \XLite\Upgrade\Cell::CORE_IDENTIFIER
                    : $key->getAuthor() . '\\' . $key->getName();

                $keyData = $key->getKeyData();

                if (
                    !empty($keyData['message'])
                    && (
                        \XLite\Upgrade\Cell::CORE_IDENTIFIER == $entityID
                        || in_array($entityID, array_keys($entries))
                    )
                ) {
                    $urlParamsAggregated[] = $this->getKeyURLParams($i++, $key);

                    if (isset($entries[$entityID])) {
                        // License key is related to upgrade entry - set blocking flag
                        $this->hasBlockedExpiredKeys = true;
                    }

                    if (\XLite\Upgrade\Cell::CORE_IDENTIFIER == $entityID) {
                        $title = 'X-Cart ' . $keyData['editionName'];

                    } else {
                        $title = isset($entries[$entityID])
                            ? sprintf('%s (%s)', $entries[$entityID]->getName(), $entries[$entityID]->getAuthor())
                            : sprintf('%s (%s)', $key->getName(), $key->getAuthor());
                    }

                    $this->expiredKeys[] = array(
                        'title'       => $title,
                        'expDate'     => \XLite\Core\Converter::formatDate($keyData['expDate']),
                        'purchaseURL' => $commonURLPart . $this->getKeyURLParams(1, $key) . '&proxy_checkout=1',
                    );
                }
            }

            $this->allKeysPurchaseURL = $urlParamsAggregated
                ? $commonURLPart . implode('&', $urlParamsAggregated) . '&proxy_checkout=1'
                : null;
        }

        return $this->expiredKeys;
    }

    /**
     * Return true if there are expired keys
     *
     * @return boolean
     */
    public function hasExpiredKeys()
    {
        return (bool) $this->getExpiredKeys();
    }

    /**
     * Get header message for block of expired keys
     *
     * @return string
     */
    public function getExpiredKeysMessage()
    {
        return \XLite\Upgrade\Cell::getInstance()->isHotfixUpdate()
            ? static::t('There are expired license keys activated in store')
            : static::t('There are expired license keys');
    }

    /**
     * Get URL to purchase all expired keys prolongation at once
     *
     * @return string
     */
    public function getAllKeysPurchaseURL()
    {
        if (!$this->allKeysPurchaseURL) {
            $this->getExpiredKeys();
        }

        return 0 < count($this->getExpiredKeys()) ? $this->allKeysPurchaseURL : null;
    }

    /**
     * Get key purchase URL parameters
     *
     * @param integer                $index Index of entity in URL
     * @param \XLite\Model\ModuleKey $key   Module key object
     *
     * @return string
     */
    protected function getKeyURLParams($index, $key)
    {
        $keyData = $key->getKeyData();

        return sprintf('add_%d=%d&lickey_%d=%s', $index, $keyData['prolongKey'], $index, md5($key->getKeyValue()));
    }

    /**
     * Check if only skins in in upgrade cell
     *
     * @return boolean
     */
    protected function isOnlySkins()
    {
        $result = false;

        $entries = \XLite\Upgrade\Cell::getInstance()->getEntries();
        if ($entries) {
            /** @var \XLite\Upgrade\Entry\Module\AModule $entry */
            foreach ($entries as $entry) {
                if ($entry->isSkinModule()) {
                    $result = true;
                } else {
                    $result = false;
                    break;
                }
            }
        }

        return $result;
    }

    // }}}
}
