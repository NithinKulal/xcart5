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
        if ($this->isUpgrade()) {
            $result = static::t(
                'X modules will be disabled',
                array('count' => $this->getIncompatibleEntriesCount())
            );

        } else {
            $result = 'Custom addons';
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
     * Return list of incompatible modules
     * 
     * @param boolean $private is return private modules
     *
     * @return array
     */
    protected function getIncompatibleEntries($private = true)
    {
        if (!isset($this->incompatibleEntries)) {
            $this->incompatibleEntries = array();

            foreach (\XLite\Upgrade\Cell::getInstance()->getIncompatibleModules() as $module) {
                if ($this->isModuleToDisable($module) || $this->isModuleCustom($module) || ($private && $this->isModulePrivate($module))) {
                    $this->incompatibleEntries[] = $module;
                }
            }
        }

        return $this->incompatibleEntries;
    }
    
    /**
     * Check for custom module
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function getModuleAuthorEmail($module)
    {
        return $module->getAuthorEmail();
    }

    /**
     * Return incompatible status message
     * 
     * @return string
     */
    protected function getIncompatibleStatusMessage()
    {
        return static::t('The module is incompatible with the new core version going to be installed');
    }

    /**
     * Return extended warranty message
     * 
     * @return string
     */
    protected function getExtendedWarrantyMessage()
    {
        return static::t('All the necessary adaptation will be done by the developer.');
    }

    /**
     * Returns incompatible entries count
     *
     * @return integer
     */
    protected function getIncompatibleEntriesCount()
    {
        return count($this->getIncompatibleEntries(false));
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
     * Check for private module
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function isModulePrivate(\XLite\Model\Module $module)
    {
        return $module->isPrivate();
    }

    /**
     * Check module for extended warranty
     *
     * @param \XLite\Model\Module $module Module to check
     *
     * @return boolean
     */
    protected function isModuleExtendedWarranty(\XLite\Model\Module $module)
    {
        return $module->isExtendedWarranty();
    }

    /**
     * Check if we upgrade core major version
     *
     * @return boolean
     */
    public function isShowCheckbox()
    {
        $cell = \XLite\Upgrade\Cell::getInstance();
        $version = $cell->getCoreMajorVersion();
        $version .= '.' . (explode('.', $cell->getCoreMinorVersion())[0] ?: '0');

        $currentVersion = \XLite::getInstance()->getMajorVersion() . '.' . \XLite::getInstance()->getMinorOnlyVersion();

        return version_compare($version, $currentVersion, '>');
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
