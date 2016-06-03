<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Upgrade\Entry\Module;

/**
 * AModule
 */
abstract class AModule extends \XLite\Upgrade\Entry\AEntry
{
    /**
     * Is fresh module installation, need for \XLite\Module\AModule::callEventInstall
     *
     * @var boolean
     */
    protected $isFreshInstall = false;

    /**
     * Method to access module main clas methods
     *
     * @param string $method Method to call
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    abstract protected function callModuleMethod($method, array $args = array());

    /**
     * Update database records
     *
     * @return array
     */
    abstract protected function updateDBRecords();

    /**
     * Get unique module entry ID
     *
     * @return string
     */
    public function getModuleEntryID()
    {
        return md5(implode('', $this->getEntryIdentityData()));
    }

    /**
     * Names of variables to serialize
     *
     * @return array
     */
    public function __sleep()
    {
        $list = parent::__sleep();
        $list[] = 'isFreshInstall';

        return $list;
    }

    /**
     * Perform upgrade
     *
     * @param boolean    $isTestMode       Flag OPTIONAL
     * @param array|null $filesToOverwrite List of custom files to overwrite OPTIONAL
     *
     * @return void
     */
    public function upgrade($isTestMode = true, $filesToOverwrite = null)
    {
        parent::upgrade($isTestMode, $filesToOverwrite);

        if (!$isTestMode) {
            list($author, $name) = explode('\\', $this->getActualName());

            if (!$this->isValid()) {
                \Includes\SafeMode::markModuleAsUnsafe($author, $name);
            }

            $this->updateDBRecords();
        }
    }

    /**
     * Call install event
     * defined in \XLite\Module\AModule::callInstallEvent()
     *
     * @return void
     */
    public function callInstallEvent()
    {
        if ($this->isFreshInstall) {
            if ($this->isInstalled()) {
                $this->callModuleMethod('callInstallEvent');
            }
        }
    }

    /**
     * Return path where the upgrade helper scripts are placed
     *
     * @return string
     */
    protected function getUpgradeHelperPath()
    {
        list($author, $name) = explode('\\', $this->getActualName());

        return \Includes\Utils\FileManager::getRelativePath(
            \Includes\Utils\ModulesManager::getAbsoluteDir($author, $name),
            LC_DIR_ROOT
        ) . LC_DS;
    }

    /**
     * Get yaml files name to run common helper 'add_labels'
     *
     * @return string
     */
    protected function getCommonHelperAddLabelsFiles()
    {
        list($author, $name) = explode('\\', $this->getActualName());
        $result = array();

        $dir = \Includes\Utils\ModulesManager::getAbsoluteDir($author, $name);

        $file = $dir . 'install.yaml';
        if (\Includes\Utils\FileManager::isExists($file)) {
            $result[] = $file;
        }

        foreach ((array) glob($dir . 'install_*.yaml') as $translationFile) {
            if (\Includes\Utils\FileManager::isExists($translationFile)) {
                $result[] = $translationFile;
            }
        }

        return $result;
    }

    /**
     * Get module identity data to generate module ID
     *
     * @return array
     */
    protected function getEntryIdentityData()
    {
        list($author, $name) = explode('\\', $this->getActualName());

        return array(
            'author'       => $author,
            'name'         => $name,
            'majorVersion' => $this->getMajorVersionOld(),
            'minorVersion' => $this->getMinorVersionOld(),
        );
    }

    /**
     * Set isFreshInstall value
     *
     * @param boolean $value New value
     *
     * @return void
     */
    protected function setIsFreshInstall($value)
    {
        $this->isFreshInstall = $value;
    }
}
