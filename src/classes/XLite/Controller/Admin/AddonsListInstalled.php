<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * AddonsListInstalled
 */
class AddonsListInstalled extends \XLite\Controller\Admin\Base\AddonsList
{
    /**
     * Internal array of data (modules modification settings)
     *
     * @var array
     */
    protected $switch = null;


    /**
     * Define and set handler attributes; initialize handler
     *
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = array())
    {
        parent::__construct($params);

        if (\XLite\Core\Session::getInstance()->returnURL) {
            $this->setReturnURL(\XLite\Core\Session::getInstance()->returnURL);
            \XLite\Core\Session::getInstance()->returnURL = '';

            $this->redirect();
        }
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isRecentlyInstalledPage()
            ? static::t('Recently installed modules')
            : static::t('Installed Modules') . ': ' . $this->getInstalledModulesCount();
    }

    /**
     * Get number of modules in the installed modules list
     *
     * @return integer
     */
    protected function getInstalledModulesCount()
    {
        $list = new \XLite\View\ItemsList\Module\Manage();
        $list->init();

        return $list->getModulesCount('');
    }

    /**
     * Substring search getter
     *
     * @return string
     */
    public function getSubstring()
    {
        return \XLite\Core\Request::getInstance()->substring;
    }
    /**
     * State search getter
     *
     * @return string
     */
    public function getState()
    {
        return \XLite\Core\Request::getInstance()->state;
    }

    /**
     * The recently installed page flag
     *
     * @return boolean
     */
    public function isRecentlyInstalledPage()
    {
        return isset(\XLite\Core\Request::getInstance()->recent)
            && (count(static::getRecentlyInstalledModuleList()) > 0);
    }

    // {{{ Short-name methods

    /**
     * Return module identificator
     *
     * @return integer
     */
    protected function getModuleId()
    {
        return \XLite\Core\Request::getInstance()->moduleId;
    }

    /**
     * Search for module
     *
     * @return \XLite\Model\Module|void
     */
    protected function getModule()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Module')->find($this->getModuleId());
    }

    /**
     * Search for modules
     *
     * @param array $data Modules data
     *
     * @return \XLite\Model\Module[]
     */
    protected function getModules($data)
    {
        $modules = array();

        foreach ((array) $data as $id => $value) {
            $modules[] = \XLite\Core\Database::getRepo('XLite\Model\Module')->find((int) $id);
        }

        return array_filter($modules);
    }

    // }}}

    // Action handlers

    /**
     * Enable module
     *
     * :TODO: TO REMOVE?
     *
     * @return void
     */
    protected function doActionEnable()
    {
        $module = $this->getModule();

        if ($module) {
            // Update data in DB
            // :TODO: this action should be performed via ModulesManager
            // :TODO: Yeah, it really should be.
            $module->setEnabled(true);
            $module->getRepository()->update($module);

            // Flag to rebuild cache
            \XLite::setCleanUpCacheFlag(true);
        }
    }

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('pack', 'uninstall_unallowed', 'disable_unallowed'));
    }

    /**
     * Pack module into PHAR module file
     *
     * @return void
     */
    protected function doActionPack()
    {
        if (LC_DEVELOPER_MODE) {
            $module = $this->getModule();

            if ($module) {
                if ($module->getEnabled()) {
                    \Includes\Utils\PHARManager::packModule(new \XLite\Core\Pack\Module($module));

                } else {
                    \XLite\Core\TopMessage::addError('Only enabled modules can be packed');
                }

            } else {
                \XLite\Core\TopMessage::addError('Module with ID X is not found', array('id' => $this->getModuleId()));
            }

        } else {
            \XLite\Core\TopMessage::addError(
                'Module packing is available in the DEVELOPER mode only. Check etc/config.php file'
            );
        }
    }

    /**
     * Uninstall module
     *
     * @return void
     */
    protected function doActionUninstall()
    {
        $module = $this->getModule();
        if ($module && $module->canUninstall()) {
            if ($module->getEnabled()) {
                $module->setEnabled(false);
                $module->callModuleMethod('callDisableEvent');
            }

            if (!defined('LC_MODULE_CONTROL')) {
                define('LC_MODULE_CONTROL', true);
            }
            $result = $this->uninstallModule($module);

            if ($result) {
                // To restore previous state
                \XLite\Core\Marketplace::getInstance()->getAddonsList(0);
                \XLite\Core\Marketplace::getInstance()->clearActionCache(\XLite\Core\Marketplace::ACTION_CHECK_FOR_UPDATES);
                \XLite\Core\Marketplace::getInstance()->clearActionCache(\XLite\Core\Marketplace::ACTION_UPDATE_PM);
                \XLite\Core\Marketplace::getInstance()->clearActionCache(\XLite\Core\Marketplace::ACTION_UPDATE_SHM);

                // Flag to rebuild cache
                \XLite::setCleanUpCacheFlag(true);
            }
        }
    }

    /**
     * Disable unallowed modules action
     *
     * @return void
     */
    public function doActionDisableUnallowed()
    {
        $switch = array();

        $list = \XLite\Core\Marketplace::getInstance()->getInactiveContentData(false);

        if ($list) {
            // Generate list of module IDs to delete
            foreach ($list as $k => $data) {
                $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneBy(
                    array(
                        'name'            => $data['name'],
                        'author'          => $data['author'],
                        'fromMarketplace' => 0,
                        'installed'       => 1,
                        'enabled'         => 1,
                    )
                );

                if ($module) {
                    $switch[$module->getModuleID()] = array(
                        'old' => 1,
                        'new' => 0,
                    );
                }
            }
        }

        if ($switch) {
            $this->switch = $switch;
            $this->doActionSwitch();

            \XLite\Core\Marketplace::getInstance()->clearActionCache(
                array(
                    \XLite\Core\Marketplace::ACTION_CHECK_FOR_UPDATES,
                    \XLite\Core\Marketplace::INACTIVE_KEYS,
                )
            );
        }
    }

    /**
     * Uninstall unallowed modules action
     *
     * @return void
     */
    public function doActionUninstallUnallowed()
    {
        $switch = array();

        $list = \XLite\Core\Marketplace::getInstance()->getInactiveContentData(false);

        if ($list) {
            // Generate list of module IDs to delete
            foreach ($list as $k => $data) {
                $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneBy(
                    array(
                        'name'            => $data['name'],
                        'author'          => $data['author'],
                        'fromMarketplace' => 0,
                        'installed'       => 1,
                        'enabled'         => 1,
                    )
                );

                if ($module) {
                    $switch[$module->getModuleID()] = array('delete' => true);
                }
            }
        }

        if ($switch) {
            $this->switch = $switch;
            $this->doActionSwitch();

            \XLite\Core\Marketplace::getInstance()->clearActionCache(
                array(
                    \XLite\Core\Marketplace::ACTION_CHECK_FOR_UPDATES,
                    \XLite\Core\Marketplace::INACTIVE_KEYS
                )
            );
        }

        // Search for inactive license keys
        $keys = \XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->findBy(array('active' => 0));
        if ($keys) {
            // Delete inactive license keys
            \XLite\Core\Database::getRepo('XLite\Model\ModuleKey')->deleteInBatch($keys);
        }
    }

    /**
     * Switch module
     *
     * @return void
     */
    public function doActionSwitch()
    {
        $changed = false;
        $deleted = false;
        $data    = $this->switch ?: (array) \XLite\Core\Request::getInstance()->switch;
        $modules = array();
        $firstModule = null;

        $switchModules = $this->getModules($data);
        $switchModulesKeys = array();

        $excludedModules = array();
        $excludedEnableModules = array();
        $excludedDisableModules = array();

        $restorePoint = \Includes\Utils\ModulesManager::getEmptyRestorePoint();

        $current = \XLite\Core\Database::getRepo('\XLite\Model\Module')->findBy(array('enabled' => true));
        foreach ($current as $module) {
            $restorePoint['current'][$module->getModuleId()] = $module->getActualName();
        }

        // Correct modules list
        foreach ($switchModules as $key => $module) {

            $toDelete  = false;
            $toDisable = false;
            $toEnable  = false;

            $switchModulesKeys[] = $module->getModuleId();

            if (!empty($data[$module->getModuleId()]['delete'])) {
                $toDelete = true;

            } else {
                $old = !empty($data[$module->getModuleId()]['old']);
                $new = !empty($data[$module->getModuleId()]['new']);
                $toDisable = (!$new && $old != $new);
                $toEnable  = ($new && $old != $new);
            }

            if ($toDisable && !$module->callModuleMethod('canDisable')) {
                $excludedDisableModules[] = $module->getModuleName();
                unset($data[$module->getModuleId()], $switchModules[$key]);

            } elseif ($toDelete || $toDisable) {

                $dependentModules = $module->getDependentModules();
                if ($dependentModules) {

                    foreach ($dependentModules as $dep) {

                        $depModule = \XLite\Core\Database::getRepo('XLite\Model\Module')->getModuleInstalled($dep);

                        if ($depModule) {
                            $depDelete = !empty($data[$depModule->getModuleId()]['delete']);
                            $depDisable = empty($data[$depModule->getModuleId()]['new']);

                            if (
                                ($toDelete && !$depDelete)
                                || ($toDisable && !$depDelete && !$depDisable)
                            ) {
                                // Remove current module from the actions list if it has active dependent modules
                                $excludedModules[] = $module->getModuleName();
                                unset($data[$module->getModuleId()], $switchModules[$key]);
                                break;
                            }
                        }
                    }
                }

            } elseif ($toEnable) {
                // Get the list of modules which current module depends on
                $list = $this->getAllDisabledModuleDependencies($module);

                if ($list) {
                    foreach ($list as $m) {
                        if (
                            empty($data[$m->getModuleId()])
                            || (
                                empty($data[$m->getModuleId()]['delete'])
                                && empty($data[$m->getModuleId()]['new'])
                            )
                        ) {
                            $data[$m->getModuleId()] = array(
                                'old' => 0,
                                'new' => 1,
                            );
                            $additionalSwitchModules[$m->getModuleId()] = $m;
                        }
                    }

                } elseif (false === $list) {
                    // Remove current module from the actions list as it can't be enabled
                    $excludedEnableModules[] = $module->getModuleName();
                    unset($data[$module->getModuleId()]);
                }
            }
        }

        if ($excludedModules) {
            \XLite\Core\TopMessage::addWarning(
                'The following selected modules cannot be disabled or uninstalled as they have dependent modules',
                array('list' => implode(', ', $excludedModules))
            );

            // Selection has excluded modules - this is a critical case, break an entire operation
            return;
        }

        if ($excludedEnableModules) {
            \XLite\Core\TopMessage::addWarning(
                'The following selected modules cannot be enabled as they depend on disabled modules which cannot be enabled',
                array('list' => implode(', ', $excludedEnableModules))
            );

            // Selection has excluded modules - this is a critical case, break an entire operation
            return;
        }

        if ($excludedDisableModules) {
            \XLite\Core\TopMessage::addWarning(
                'The following selected modules cannot be disabled due to architecture limitations',
                array('list' => implode(', ', $excludedDisableModules))
            );

            // Selection has excluded modules - this is a critical case, break an entire operation
            return;
        }

        if (!empty($additionalSwitchModules)) {
            // Extend $switchModules list by additional modules
            foreach ($additionalSwitchModules as $k => $am) {
                if (!in_array($k, $switchModulesKeys)) {
                    $switchModules[] = $am;
                }
            }
        }        

        foreach ($switchModules as $module) {

            if (!empty($data[$module->getModuleId()]['delete'])) {
                $old = $new = null;
                $delete = true;

            } else {
                $old = !empty($data[$module->getModuleId()]['old']);
                $new = !empty($data[$module->getModuleId()]['new']);
                $delete = false;                
            }

            if ($delete) {

                // Uninstall module

                if ($module->getEnabled()) {
                    $module->setEnabled(false);
                    $module->callModuleMethod('callDisableEvent');
                }

                if (!defined('LC_MODULE_CONTROL')) {
                    define('LC_MODULE_CONTROL', true);
                }

                if ($this->uninstallModule($module)) {
                    // Module has been successfully removed
                    $deleted = true;
                    $restorePoint['deleted'][] = $module->getActualName();

                } else {
                    // If module could not be removed...
                    $modules[] = $module;
                    $changed = true;
                }

            } elseif ($old !== $new) {

                // Change module status

                $module->setEnabled($new);

                // Call disable event to make some module specific changes
                if ($old) {
                    $module->callModuleMethod('callDisableEvent');
                } elseif (is_null($firstModule)) {
                    $firstModule = $module;
                }

                if ($new) {
                    $restorePoint['enabled'][$module->getModuleId()] = $module->getActualName();
                } else {  
                    $restorePoint['disabled'][$module->getModuleId()] = $module->getActualName();
                }

                $modules[] = $module;
                $changed = true;
            }
        }       

        // Flag to rebuild cache
        if ($changed) {
            // We redirect the admin to the modules page on the activated module anchor
            // The first module in a batch which is available now
            \XLite\Core\Session::getInstance()->returnURL = $firstModule
                ? $this->getModulePageURL($firstModule)
                : (\XLite\Core\Request::getInstance()->return ?: '');

            \XLite\Core\Database::getRepo('\XLite\Model\Module')->updateInBatch($modules);
        }

        if ($deleted) {
            // Refresh marketplace modules cache
            \XLite\Core\Marketplace::getInstance()->getAddonsList(0);
            \XLite\Core\Marketplace::getInstance()->clearActionCache(\XLite\Core\Marketplace::ACTION_CHECK_FOR_UPDATES);
            \XLite\Core\Marketplace::getInstance()->clearActionCache(\XLite\Core\Marketplace::ACTION_UPDATE_PM);
            \XLite\Core\Marketplace::getInstance()->clearActionCache(\XLite\Core\Marketplace::ACTION_UPDATE_SHM);
        }

        if ($changed || $deleted) {
            // Flag to rebuild classes cache
            \XLite::setCleanUpCacheFlag(true);

            \XLite\Core\Marketplace::getInstance()->clearActionCache(
                array(
                    \XLite\Core\Marketplace::ACTION_CHECK_FOR_UPDATES,
                    \XLite\Core\Marketplace::INACTIVE_KEYS
                )
            );
        }

        \Includes\Utils\ModulesManager::updateModuleMigrationLog($restorePoint);
    }

    /**
     * Get list of all not active module dependencies
     * Returns false if module can not be enabled
     *
     * @param \XLite\Model\Module $module Module model
     *
     * @return array|boolean
     */
    protected function getAllDisabledModuleDependencies($module)
    {
        $list = array();
        $canEnable = true;

        foreach ($module->getDependencyModules(true) as $dep) {

            if (!$this->canEnable($dep)) {
                $canEnable = false;
                break;

            } else {

                $list[$dep->getActualName()] = $dep;

                $deps = $this->getAllDisabledModuleDependencies($dep);

                if (false === $deps) {
                    $canEnable = false;
                    break;

                } else {
                    $list = array_merge($list, $deps);
                }
            }
        }

        return $canEnable ? $list : false;
    }

    /**
     * Alias for canEnable() method
     *
     * @param \XLite\Model\Module $module Module model
     *
     * @return boolean
     */
    protected function canEnable($module)
    {
        return $module->canEnable(true);
    }

    /**
     * Module page URL getter
     *
     * @param \XLite\Model\Module $module Module model
     *
     * @return string
     */
    protected function getModulePageURL(\XLite\Model\Module $module)
    {
        return $module->getInstalledURL();
    }

    /**
     * Perform some actions before redirect
     *
     * @param string $action Performed action
     *
     * @return void
     */
    protected function actionPostprocess($action)
    {
        parent::actionPostprocess($action);

        $this->setReturnURL($this->buildURL('addons_list_installed'));
    }

    // }}}
}
