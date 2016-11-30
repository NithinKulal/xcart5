<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Module;

/**
 * Abstract product list
 */
abstract class AModule extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Widget param names
     */
    const PARAM_SUBSTRING   = 'substring';
    const PARAM_STATE       = 'state';

    /**
     * Sort option name definitions
     */
    const SORT_OPT_ALPHA  = 'm.moduleName';
    const SORT_OPT_NEWEST = 'm.revisionDate';

    /**
     * High popularity level
     */
    const MAX_POPULAR_LEVEL = 0.4;

    /**
     * Cache value for maximum popularity level
     *
     * @var float
     */
    protected $maximumPopularity;

    /**
     * List of core versions to update
     *
     * @var array
     */
    protected $coreVersions;

    /**
     * Promo banner info cache
     *
     * @var boolean|array
     */
    protected $bannerInfo = null;

    /**
     * Update status flags (result of 'check_for_update' request to the marketplace)
     *
     * @var array
     */
    protected $updateFlags = null;


    /**
     * Check if the module is installed
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    abstract protected function isInstalled(\XLite\Model\Module $module);

    /**
     * Define and set widget attributes; initialize widget
     *
     * @param array $params Widget params OPTIONAL
     *
     * @return void
     */
    public function __construct(array $params = array())
    {
        $this->sortByModes += $this->getSortOptions();

        parent::__construct($params);
    }

    /**
     * getSortByModeDefault
     *
     * @return string
     */
    protected function getSortByModeDefault()
    {
        return static::SORT_OPT_NEWEST;
    }

    /**
     * getSortOrder
     *
     * @return string
     */
    protected function getSortOrder()
    {
        return static::SORT_OPT_ALPHA === $this->getSortBy() ? static::SORT_ORDER_ASC : static::SORT_ORDER_DESC;
    }

    /**
     * Return list of sort options
     *
     * @return array
     */
    protected function getSortOptions()
    {
        return array(
            static::SORT_OPT_ALPHA => static::t('module-sort-Alphabetically'),
        );
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        if (static::SORT_OPT_NEWEST == $this->getSortBy()) {
            // Newest sorting is also sorted by module name
            $cnd->{\XLite\Model\Repo\Module::P_ORDER_BY} = array(
                array(static::SORT_OPT_NEWEST, $this->getSortOrder()),
                array(static::SORT_OPT_ALPHA, self::SORT_ORDER_ASC),
            );
        } else {
            $cnd->{\XLite\Model\Repo\Module::P_ORDER_BY} = array($this->getSortBy(), $this->getSortOrder());
        }

        return $cnd;
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.module';
    }

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/module';
    }

    /**
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return $this->getDir() . '/' . $this->getPageBodyDir();
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return null;
    }

    /**
     * getJSHandlerClassName
     *
     * @return string
     */
    protected function getJSHandlerClassName()
    {
        return 'ModulesList';
    }

    /**
     * Check if there are some errors for the current module
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function hasErrors(\XLite\Model\Module $module)
    {
        return !$this->canEnable($module);
    }

    /**
     * Check if the module can be enabled
     *
     * @param \XLite\Model\Module $module    Module
     * @param boolean             $safeCheck True - check if dependent modules can be enabled, false - dependent modules must be active
     *
     * @return boolean
     */
    protected function canEnable(\XLite\Model\Module $module, $safeCheck = false)
    {
        return $module->canEnable($safeCheck);
    }

    /**
     * Check if the module can be disabled
     *
     * @param \XLite\Model\Module $module Module
     * @param boolean             $safeCheck True - check if dependent modules can be disabled, false - dependent modules must be active
     *
     * @return boolean
     */
    protected function canDisable(\XLite\Model\Module $module, $safeCheck = false)
    {
        return $module->canDisable($safeCheck);
    }

    /**
     * Check if the module is enabled
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return boolean
     */
    protected function isEnabled(\XLite\Model\Module $module)
    {
        $installed = $this->getModuleInstalled($module);

        return isset($installed) && $installed->getEnabled();
    }

    /**
     * Return modules list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $result = \XLite\Core\Database::getRepo('\XLite\Model\Module')->search($cnd, $countOnly);

        if (is_array($result)) {
            foreach ($result as $k => $module) {
                if ($module->callModuleMethod('isSystem')) {
                    unset($result[$k]);
                }
            }
        }

        return $result;
    }

    // {{{ Version-related checks

    /**
     * Check if the module major version is the same as the core one.
     * Alias
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function isModuleCompatible(\XLite\Model\Module $module)
    {
        return $module->isModuleCompatible();
    }

    /**
     * Check if the module is part of X-Cart 5 license
     *
     * @param \XLite\Model\Module $module Module entity
     *
     * @return boolean
     */
    protected function isXCN(\XLite\Model\Module $module)
    {
        return $module->isAvailable() && \XLite\Model\Module::NOT_XCN_MODULE < intval($module->getXcnPlan());
    }

    /**
     * Get list of editions where module is allowed to be installed.
     * This list is used for labels 'Module available editions 3' and 'Module available editions 4'
     *
     * @param \XLite\Model\Module $module    Module entity
     * @param boolean             $withLinks Return list of licenses with links to purchase pages
     *
     * @return string
     */
    protected function getEditions(\XLite\Model\Module $module, $withLinks = false)
    {
        $result = '';

        $editions = $module->getEditions();

        if (empty($editions)) {
            // Module editions is empty array - initialize $editions with single edition (Business)
            // see BUG-1262 for the details
            $editions = array('391_Business');
        }

        if ($editions) {

            foreach ($editions as $k => $v) {
                if (preg_match('/^(\d+)_(.+)$/', $v, $match) && 0 < intval($match[1])) {
                    $editions[$k] = sprintf(
                        '<a href="%s" target="_blank">X-Cart %s</a>',
                        \XLite\Core\Marketplace::getPurchaseURL($match[1]),
                        $match[2]
                    );
                }
            }

            $result = array_shift($editions);

            if ($editions) {
                $last = ' ' . static::t('or') . ' ' . array_pop($editions);

                $middle = $editions
                    ? ', ' . implode(', ', $editions)
                    : '';

                $result = $result . $middle . $last;
            }
        }

        return $result;
    }

    /**
     * URL of the page where license can be purchased
     *
     * @return string
     */
    protected function getPurchaseURL()
    {
        return \XLite\Core\Marketplace::getPurchaseURL();
    }

    /**
     * Check if module requires new core version.
     * Alias
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function isCoreUpgradeNeeded(\XLite\Model\Module $module)
    {
        return $this->checkModuleMajorVersion($module, '<')
            || (
                $this->checkModuleMajorVersion($module, '=')
                && $module->getMinorRequiredCoreVersion() !== 0
                && $this->checkModuleMinorVersion($module, '<')
            );
    }

    /**
     * Get core version needed for the module.
     *
     * The module has major version (must be equal to the core version to install)
     * The module can have minor required core version.
     * In this case the same minor version of core is needed.
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return string
     */
    protected function getNeededCoreVersion(\XLite\Model\Module $module)
    {
        if ($this->checkModuleMajorVersion($module, '<')) {
            $majorVer = $module->getMajorVersion();

            $minorVer = $module->getMinorRequiredCoreVersion() !== 0
                // Minor core version must be the same as minor required core version
                ? $module->getMinorRequiredCoreVersion()
                // The first minor version must be compatible with the module
                : '0';
        } else {
            $majorVer = \XLite::getInstance()->getMajorVersion();

            $minorVer = $module->getMinorRequiredCoreVersion() !== 0 && $this->checkModuleMinorVersion($module, '<')
                // Minor core version must be the same as minor required core version
                ? $module->getMinorRequiredCoreVersion()
                // Impossible case in the real world, since the
                : '0';
        }

        return $majorVer . '.' . $minorVer;
    }

    /**
     * Check if core requires new module version.
     * Alias
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function isModuleUpgradeNeeded(\XLite\Model\Module $module)
    {
        return $this->checkModuleMajorVersion($module, '>');
    }

    /**
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function isUpgradeRequestAvailable(\XLite\Model\Module $module)
    {
        $isInternalModule = in_array($module->getAuthor(), ['XC', 'CDev']);

        return $this->isModuleUpgradeNeeded($module)
            && !$this->isCoreUpgradeNeeded($module)
            && !$isInternalModule
            && \XLite\Core\Marketplace::getInstance()->isUpgradeRequestAvailable($module);
    }

    /**
     * Compare module version with the core one
     *
     * @param \XLite\Model\Module $module   Module to check
     * @param string              $operator Comparison operator
     *
     * @return boolean
     */
    protected function checkModuleMajorVersion(\XLite\Model\Module $module, $operator)
    {
        return \XLite::getInstance()->checkVersion($module->getMajorVersion(), $operator);
    }

    /**
     * Compare module version with the core one
     *
     * @param \XLite\Model\Module $module   Module to check
     * @param string              $operator Comparison operator
     *
     * @return boolean
     */
    protected function checkModuleMinorVersion(\XLite\Model\Module $module, $operator)
    {
        return \XLite::getInstance()->checkMinorVersion($module->getMinorRequiredCoreVersion(), $operator);
    }

    /**
     * Return list of modules current module depends on
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return array
     */
    protected function getDependencyModules(\XLite\Model\Module $module)
    {
        return $module->getDependencyModules(true);
    }

    /**
     * Return list of modules current module requires to be disabled
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return array
     */
    protected function getEnabledMutualModules(\XLite\Model\Module $module)
    {
        return $module->getEnabledMutualModules();
    }

    /**
     * Check if there are modules current module depends on
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return array
     */
    protected function hasWrongDependencies(\XLite\Model\Module $module)
    {
        return $module->hasWrongDependencies();
    }

    // }}}

    // {{{ Methods to search modules of certain types

    /**
     * Check if core requires new (but the same as core major) version of module
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    abstract protected function isModuleUpdateAvailable(\XLite\Model\Module $module);

    /**
     * Return list of core versions for update
     *
     * @return array
     */
    protected function getCoreVersions()
    {
        if (!isset($this->coreVersions)) {
            $this->coreVersions = (array) \XLite\Core\Marketplace::getInstance()->getCores();
        }

        return $this->coreVersions;
    }

    /**
     * Is core upgrade available
     *
     * @param string $majorVersion core version to check
     *
     * @return void
     */
    protected function isCoreUpgradeAvailable($majorVersion)
    {
        return (bool) \Includes\Utils\ArrayManager::getIndex($this->getCoreVersions(), $majorVersion, true);
    }

    /**
     * Check if there are updates (new core revision and/or module revisions)
     *
     * @return boolean
     */
    protected function areUpdatesAvailable()
    {
        if (!isset($this->updateFlags)) {
            $this->updateFlags = \XLite\Core\Marketplace::getInstance()->checkForUpdates();
            if (!is_array($this->updateFlags)) {
                $this->updateFlags = array();
            }
        }

        return !empty($this->updateFlags[\XLite\Core\Marketplace::FIELD_ARE_UPDATES_AVAILABLE]);
    }

    /**
     * Search for module for update. Alias
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return \XLite\Model\Module
     */
    protected function getModuleForUpdate(\XLite\Model\Module $module)
    {
        return $module->getRepository()->getModuleForUpdate($module);
    }

    /**
     * Search for module for update. Alias
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return \XLite\Model\Module
     */
    protected function getModuleForUpgrade(\XLite\Model\Module $module)
    {
        return $module->getRepository()->getModuleForUpgrade($module);
    }

    /**
     * Search for module from marketplace. Alias
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return \XLite\Model\Module
     */
    protected function getModuleFromMarketplace(\XLite\Model\Module $module)
    {
        return $module->getRepository()->getModuleFromMarketplace($module);
    }

    /**
     * Search for installed module
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return \XLite\Model\Module
     */
    protected function getModuleInstalled(\XLite\Model\Module $module)
    {
        return $module->getRepository()->getModuleInstalled($module);
    }

    /**
     * Get module version. Alias
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return string
     */
    protected function getModuleVersion(\XLite\Model\Module $module)
    {
        return $module->getVersion();
    }

    /**
     * Get current tag
     *
     * @return string
     */
    protected function getTag()
    {
        $tag = \XLite\Core\Request::getInstance()->tag;

        if (empty($tag) || !in_array($tag, array_keys($this->getTags()))) {
            $tag = '';
        }

        return $tag;
    }

    /**
     * Return tags array
     *
     * @return array
     */
    protected function getTags()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Module')->getTags();
    }

    /**
     * Return tags array
     *
     * @return array
     */
    protected function getVendors()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Module')->getVendors();
    }

    // }}}

    // {{{ Dependency statuses

    /**
     * Get all data to dependency item in list
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return array
     */
    protected function getDependencyData(\XLite\Model\Module $module)
    {
        $cacheKey = \Includes\Utils\ModulesManager::getActiveModulesHash() . $module->getActualName() . 'dependencyData';
        $cacheDriver = \XLite\Core\Database::getCacheDriver();

        if ($cacheDriver->contains($cacheKey)) {
            $result = $cacheDriver->fetch($cacheKey);
        } else{
            if ($module->isPersistent()) {
                if ($module->getInstalled()) {
                    if ($module->getEnabled()) {
                        $result = array('status' => 'enabled', 'class' => 'good');
                    } else {
                        $result = array('status' => 'disabled', 'class' => 'none');
                    }

                    $result['href'] = $this->getModulePageURL($module);

                } else {
                    $url  = $this->buildURL('addons_list_marketplace', '', array('substring' => $module->getModuleName()));
                    $url .= '#' . $module->getName();

                    $result = array('href' => $url, 'status' => 'not installed', 'class' => 'none');
                }

            } else {
                $result = array('status' => 'unknown', 'class' => 'poor');
            }
            $cacheDriver->save($cacheKey, $result);
        }

        return $result;
    }

    /**
     * Module page URL getter
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return string
     */
    protected function getModulePageURL(\XLite\Model\Module $module)
    {
        return $module->getInstalledURL();
    }

    /**
     * Get number of items per page for the modules list
     *
     * @return integer
     */
    public function getPagerParamItemsPerPage()
    {
        return $this->getSavedRequestParam(\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE)
            ?: $this->getPager()->getItemsPerPage();
    }

    /**
     * Get some data for depenendecy in list
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return string
     */
    protected function getDependencyHRef(\XLite\Model\Module $module)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getDependencyData($module), 'href', true);
    }

    /**
     * Get some data for depenendecy in list
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return string
     */
    protected function getDependencyStatus(\XLite\Model\Module $module)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getDependencyData($module), 'status', true);
    }

    /**
     * Get some data for depenendecy in list
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return string
     */
    protected function getDependencyCSSClass(\XLite\Model\Module $module)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->getDependencyData($module), 'class', true);
    }

    // }}}

    /**
     * Get search substring value
     *
     * @return string
     */
    protected function getSearchSubstring()
    {
        return \XLite\Core\Request::getInstance()->substring;
    }

    /**
     * Returns the maximum popularity counter (uses the cache class variable)
     *
     * @return integer
     */
    protected function getMaximumPopularity()
    {
        if (!isset($this->maximumPopularity)) {
            $this->maximumPopularity = \XLite\Core\Database::getRepo('XLite\Model\Module')->getMaximumDownloads();
        }

        return $this->maximumPopularity;
    }

    /**
     * Defines specific downloads CSS class if necessary
     *
     * @param \XLite\Model\Module $module
     *
     * @return string
     */
    protected function getDownloadsCSSClass(\XLite\Model\Module $module)
    {
        return (($module->getDownloads() / $this->getMaximumPopularity()) >= static::MAX_POPULAR_LEVEL)
            ? ' high-popular'
            : '';
    }

    /**
     * Get Store URL
     *
     * @return string
     */
    protected function getStoreURL()
    {
        return \XLite\Core\URLManager::getShopURL(\XLite\Core\Converter::buildURL());
    }

    /**
     * Get user email
     *
     * @return string
     */
    protected function getUserEmail()
    {
        return \XLite\Core\Auth::getInstance()->getProfile()->getLogin();
    }

    /**
     * Get module page URL
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return string
     */
    protected function getPageURL(\XLite\Model\Module $module)
    {
        $result = $module->getPageURL();

        if (!$result) {
            $mpModule = $this->getModuleFromMarketplace($module);

            if ($mpModule) {
                $result = $mpModule->getPageURL();
            }
        }

        return $result ? \XLite::getXCartURL($result) : '';
    }

    /**
     * Get module author URL
     *
     * @param \XLite\Model\Module $module Module
     *
     * @return string
     */
    protected function getAuthorURL(\XLite\Model\Module $module)
    {
        $result = $module->getAuthorEmail();

        if (!$result) {
            $mpModule = $this->getModuleFromMarketplace($module);

            if ($mpModule) {
                $result = $mpModule->getAuthorEmail();
            }
        }

        if (filter_var($result, FILTER_VALIDATE_URL)) {

        } elseif (filter_var($result, FILTER_VALIDATE_EMAIL)) {
            $result = 'mailto:' . $result;

        } else {
            $result = null;
        }

        return $result;
    }
}
