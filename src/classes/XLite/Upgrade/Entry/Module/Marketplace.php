<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Upgrade\Entry\Module;

/**
 * Marketplace
 */
class Marketplace extends \XLite\Upgrade\Entry\Module\AModule
{
    /**
     * Error license text from marketplace
     */
    const LICENSE_ERROR = 'Error code (1027): No valid X-Cart license for this module';

    /**
     * Identifier for installed module
     *
     * @var array
     */
    protected $moduleInfoInstalled;

    /**
     * Identifier for upgrade module
     *
     * @var array
     */
    protected $moduleInfoForUpgrade;

    /**
     * Old major version (cache)
     *
     * :WARNING: do not remove this variable:
     * it's required for the proper upgrade process
     *
     * @var string
     */
    protected $majorVersionOld;

    /**
     * Old minor version (cache)
     *
     * :WARNING: do not remove this variable:
     * it's required for the proper upgrade process
     *
     * @var string
     */
    protected $minorVersionOld;

    /**
     * Return module actual name
     *
     * @return string
     */
    public function getActualName()
    {
        return $this->getModuleInstalled()->getActualName();
    }

    /**
     * Return entry readable name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getModuleForUpgrade()->getModuleName();
    }

    /**
     * Return icon URL
     *
     * @return string
     */
    public function getIconURL()
    {
        return $this->getModuleForUpgrade()->getPublicIconURL();
    }

    /**
     * Return marketplace ID
     *
     * @return string
     */
    public function getMarketplaceID()
    {
        return $this->getModuleForUpgrade()->getMarketplaceID();
    }

    /**
     * Return entry old major version
     *
     * @return string
     */
    public function getMajorVersionOld()
    {
        if (!isset($this->majorVersionOld)) {
            $this->majorVersionOld = $this->getModuleInstalled()->getMajorVersion();
        }

        return $this->majorVersionOld;
    }

    /**
     * Return entry old minor version
     *
     * @return string
     */
    public function getMinorVersionOld()
    {
        if (!isset($this->minorVersionOld)) {
            $this->minorVersionOld = $this->getModuleInstalled()->getFullMinorVersion();
        }

        return $this->minorVersionOld;
    }

    /**
     * Return entry new major version
     *
     * @return string
     */
    public function getMajorVersionNew()
    {
        return $this->getModuleForUpgrade()->getMajorVersion();
    }

    /**
     * Return entry new minor version
     *
     * @return string
     */
    public function getMinorVersionNew()
    {
        return $this->getModuleForUpgrade()->getFullMinorVersion();
    }

    /**
     * Return entry revision date
     *
     * @return integer
     */
    public function getRevisionDate()
    {
        $this->getModuleForUpgrade()->getRevisionDate();
    }

    /**
     * Return module author readable name
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->getModuleForUpgrade()->getAuthorName();
    }

    /**
     * Check if module is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool) $this->getModuleInstalled()->getEnabled();
    }

    /**
     * Check if module is installed
     *
     * @return boolean
     */
    public function isInstalled()
    {
        return (bool) $this->getModuleInstalled()->getInstalled();
    }

    /**
     * Check if module is skin
     *
     * @return boolean
     */
    public function isSkinModule()
    {
        return $this->getModuleInstalled()->isSkinModule();
    }

    /**
     * Check if entry is system module
     *
     * @return boolean
     */
    public function isSystem()
    {
        return (bool) $this->getModuleInstalled()->getIsSystem();
    }

    /**
     * Return entry pack size
     *
     * @return integer
     */
    public function getPackSize()
    {
        return $this->getModuleForUpgrade()->getPackSize();
    }

    /**
     * Module page URL getter
     *
     * @return string
     */
    public function getInstalledURL()
    {
        $result = '';

        if ($this->isInstalled()) {
            $result = $this->getModuleInstalled()->getInstalledURL();
        }

        return $result;
    }


    /**
     * Do specific directory preparations after unpacking
     * Actually we add the disabled module with the active upgrade helpers (post_rebuild, pre_upgrade, post_upgrade)
     * into a special module list which will be either enabled or uninstall before the upgrade process
     *
     * @param string $dir Directory
     *
     * @return void
     */
    protected function prepareUnpackDir($dir)
    {
        if (!$this->isEnabled()) {

            $this->hasUpgradeHelpers()
                && \XLite\Upgrade\Cell::getInstance()->addDisabledModulesHook(
                    $this->getModuleInstalled()->getMarketplaceID()
                );

            $this->hasPreUpgradeHelpers()
                && \XLite::getInstance()->checkVersion($this->getMajorVersionOld(), '>')
                && \XLite\Upgrade\Cell::getInstance()->addPreUpgradeWarningModules(
                    $this->getModuleForUpgrade()->getMarketplaceID()
                );
        }
    }

    /**
     * Defines the helpers types to check
     *
     * @return array
     */
    protected function getHelpersToCheck()
    {
        return $this->getHelperTypes();
    }

    /**
     * Checks if the module has any defined upgrade helpers
     *
     * @return boolean
     */
    protected function hasUpgradeHelpers()
    {
        $result = false;
        foreach ($this->getHelpersToCheck() as $type) {
            $helpers = $this->getHelpers($type);

            $result = $result || !empty($helpers);
        }

        return $result;
    }

    /**
     * Checks if the module has pre-upgrade helpers
     *
     * @return boolean
     */
    protected function hasPreUpgradeHelpers()
    {
        $helpers = $this->getHelpers('pre_upgrade');

        return !empty($helpers);
    }

    /**
     * Download hashes for current version
     *
     * @return array
     */
    protected function loadHashesForInstalledFiles()
    {
        $licenseKey = $this->getModuleForUpgrade()->getLicenseKey();

        $result = \XLite\Core\Marketplace::getInstance()->getAddonHash(
            $this->getModuleInstalled()->getMarketplaceID(),
            $licenseKey ? $licenseKey->getKeyValue() : null
        );

        if (!$result) {

            $params = array(
                'name'    => $this->getActualName(),
                'version' => $this->getModuleInstalled()->getVersion(),
            );

            $this->addFileErrorMessage(
                'Module ({{name}} v{{version}}) not found on marketplace (hash is not received)',
                \XLite\Core\Marketplace::getInstance()->getError(),
                true,
                $params
            );
        }

        return $result;
    }

    /**
     * Constructor
     *
     * @param \XLite\Model\Module $moduleInstalled  Module model object
     * @param \XLite\Model\Module $moduleForUpgrade Module model object
     */
    public function __construct(\XLite\Model\Module $moduleInstalled, \XLite\Model\Module $moduleForUpgrade)
    {
        $this->moduleInfoInstalled  = $this->getPreparedModuleInfo($moduleInstalled, false);
        $this->moduleInfoForUpgrade = $this->getPreparedModuleInfo($moduleForUpgrade, true);

        if (is_null($this->getModuleInstalled())) {
            \Includes\ErrorHandler::fireError(
                'Module ["' . implode('", "', $this->moduleInfoInstalled) . '"] is not found in DB'
            );
        }

        if (is_null($this->getModuleForUpgrade())) {
            \Includes\ErrorHandler::fireError(
                'Module ["' . implode('", "', $this->moduleInfoForUpgrade) . '"] is not found in DB'
                . ' or is not a marketplace module'
            );
        }

        parent::__construct();
    }

    /**
     * Names of variables to serialize
     *
     * @return array
     */
    public function __sleep()
    {
        $list = parent::__sleep();
        $list[] = 'moduleInfoInstalled';
        $list[] = 'moduleInfoForUpgrade';

        return $list;
    }

    /**
     * Download package
     *
     * @return boolean
     */
    public function download()
    {
        $result = false;
        $licenseKey = $this->getModuleForUpgrade()->getLicenseKey();

        $path = \XLite\Core\Marketplace::getInstance()->getAddonPack(
            $this->getMarketplaceID(),
            $licenseKey ? $licenseKey->getKeyValue() : null
        );

        $params = array('name' => $this->getActualName());

        if (isset($path)) {
            $this->addFileInfoMessage('Module pack ("{{name}}") is received', $path, true, $params);

            $this->setRepositoryPath($path);
            $this->saveHashesForInstalledFiles();

            $result = parent::download();

        } else {
            $error = \XLite\Core\Marketplace::getInstance()->getError();

            if (static::LICENSE_ERROR === $error) {
                $this->addToPremiumLicenseModules();
            } else {
                $this->addFileErrorMessage(
                    'Module pack ("{{name}}") is not received',
                    $error,
                    true,
                    $params
                );
            }
        }

        return $result;
    }

    /**
     * Prepare and return module identity data
     *
     * @param \XLite\Model\Module $module          Module to get info
     * @param boolean             $fromMarketplace Flag
     *
     * @return array
     */
    protected function getPreparedModuleInfo(\XLite\Model\Module $module, $fromMarketplace)
    {
        // :WARNING: do not change the summands order:
        // it's important for the "updateDBRecords()" method
        return array('fromMarketplace' => $fromMarketplace) + $module->getIdentityData();
    }

    /**
     * Search for module in DB
     *
     * @param array $moduleInfo Info to search by
     *
     * @return \XLite\Model\Module
     */
    protected function getModule(array $moduleInfo)
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Module')->findOneBy($moduleInfo);
    }

    /**
     * Method to access module main class methods
     *
     * @param string $method Method to call
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    protected function callModuleMethod($method, array $args = array())
    {
        return \Includes\Utils\ModulesManager::callModuleMethod($this->getActualName(), $method, $args);
    }

    /**
     * Alias
     *
     * :WARNING: do not cache this object: identity info may be changed
     *
     * @return \XLite\Model\Module
     */
    protected function getModuleInstalled()
    {
        return $this->getModule($this->moduleInfoInstalled) ?: $this->getModuleForUpgrade();
    }

    /**
     * Alias
     *
     * :WARNING: do not cache this object: identity info may be changed
     *
     * @return \XLite\Model\Module
     */
    protected function getModuleForUpgrade()
    {
        return $this->getModule($this->moduleInfoForUpgrade);
    }

    /**
     * Update database records
     *
     * @return array
     */
    protected function updateDBRecords()
    {
        $this->setIsFreshInstall(!$this->isInstalled());

        $forUpgrade = $this->getModuleForUpgrade();
        $installed  = $this->getModuleInstalled();

        $forUpgrade->setInstalled(true);
        $isSkinModule = $this->isSkinModule();

        if ($forUpgrade->getIdentityData() !== $installed->getIdentityData()) {
            $forUpgrade->setEnabled($installed->getEnabled());
            $forUpgrade->setYamlLoaded($installed->getYamlLoaded());

            \XLite\Core\Database::getRepo('XLite\Model\Module')->delete($installed);

            $this->moduleInfoInstalled = $this->getPreparedModuleInfo($forUpgrade, false);

        } else {
            $forUpgrade->setEnabled(!$isSkinModule);
        }

        $forUpgrade->setInstalled(true);
        $forUpgrade->setIsSkin($isSkinModule);
        $forUpgrade->setFromMarketplace(false);
        $this->moduleInfoForUpgrade['fromMarketplace'] = false;

        \XLite\Core\Database::getRepo('XLite\Model\Module')->update($forUpgrade);
    }
}
