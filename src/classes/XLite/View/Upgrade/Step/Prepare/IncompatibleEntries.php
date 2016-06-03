<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Upgrade\Step\Prepare;

/**
 * IncompatibleEntries
 */
class IncompatibleEntries extends \XLite\View\Upgrade\Step\Prepare\APrepare
{
    /**
     * Incompatible entries
     *
     * @var array
     */
    protected $incompatibleEntries = null;

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/widget.js';

        return $list;
    }

    /**
     * Get directory where template is located (body.twig)
     *
     * @return string
     */
    protected function getDir()
    {
        return $this->isUpgrade()
            ? parent::getDir() . '/incompatible_entries_upgrade'
            : parent::getDir() . '/incompatible_entries_update';
    }

    /**
     * Return internal list name
     *
     * @return string
     */
    protected function getListName()
    {
        return $this->isUpgrade()
            ? parent::getListName() . '.incompatible_entries_upgrade'
            : parent::getListName() . '.incompatible_entries_update';

    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        if (\XLite\Upgrade\Cell::getInstance()->isUpgrade()) {
            $result = static::t(
                'X modules will be disabled',
                array('count' => $this->getIncompatibleEntriesCount())
            );

        } else {
            $result = 'These components require your attention';
        }

        return $result;
    }


    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && (bool)$this->getIncompatibleEntries();
    }

    /**
     * Return list of inclompatible modules
     *
     * @return array
     */
    protected function getIncompatibleEntries()
    {
        if (!isset($this->incompatibleEntries)) {
            $this->incompatibleEntries = array();

            foreach (\XLite\Upgrade\Cell::getInstance()->getIncompatibleModules() as $module) {
                if ($this->isModuleToDisable($module) || $this->isModuleCustom($module)) {
                    $this->incompatibleEntries[] = $module;
                }
            }
        }

        return $this->incompatibleEntries;
    }

    /**
     * Returns incompatible entries count
     *
     * @return integer
     */
    protected function getIncompatibleEntriesCount()
    {
        return count($this->getIncompatibleEntries());
    }

    /**
     * Check if there is any disabled entry in the module list
     *
     * @return boolean
     */
    protected function hasDisabledEntries()
    {
        $result = false;

        foreach (\XLite\Upgrade\Cell::getInstance()->getIncompatibleModules() as $module) {
            if ($this->isModuleToDisable($module)) {
                $result = true;
                break;
            }
        }

        return $result;
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

        $classActual = preg_replace('/(.*)Abstract$/i', '$1', $classActual);

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
     * Check for request for upgrade availability
     *
     * @return boolean
     */
    protected function isRequestForUpgradeAvailable()
    {
        $result = false;

        if (
            \XLite\Upgrade\Cell::getInstance()->hasCoreUpdate()
            && \XLite::getXCNLicense()
        ) {
            foreach ($this->getIncompatibleEntries() as $module) {
                if (!$module->isCustom()) {
                    $result = true;

                    break;
                }
            }
        }

        return $result;
    }
}
