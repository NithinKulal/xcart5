<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Pack;

/**
 * Module
 */
class Module extends \XLite\Core\Pack\APack
{
    /**
     * Field names in metadata
     */
    const METADATA_FIELD_ACTUAL_NAME   = 'ActualName';
    const METADATA_FIELD_VERSION_MINOR = 'VersionMinor';
    const METADATA_FIELD_VERSION_MAJOR = 'VersionMajor';
    const METADATA_FIELD_VERSION_BUILD = 'VersionBuild';
    const METADATA_FIELD_MIN_CORE_VERSION = 'MinCoreVersion';
    const METADATA_FIELD_NAME          = 'Name';
    const METADATA_FIELD_AUTHOR        = 'Author';
    const METADATA_FIELD_ICON_LINK     = 'IconLink';
    const METADATA_FIELD_DESCRIPTION   = 'Description';
    const METADATA_FIELD_DEPENDENCIES  = 'Dependencies';
    const METADATA_FIELD_IS_SYSTEM     = 'isSystem';

    /**
     * Current module
     *
     * @var \XLite\Model\Module
     */
    protected $module;

    // {{{ Public methods

    /**
     * Constructor
     *
     * @param \XLite\Model\Module $module Current module
     *
     * @return void
     */
    public function __construct(\XLite\Model\Module $module)
    {
        $this->module = $module;
    }

    /**
     * Return pack name
     *
     * @return string
     */
    public function getName()
    {
        // It's the fix for PHAR::compress(): it's triming dots in file names
        return str_replace('\\', '-', $this->module->getActualName())
            . '-v' . str_replace('.', '_', $this->module->callModuleMethod('getVersion'));
    }

    /**
     * Return iterator to walk through directories
     *
     * @return \AppendIterator
     */
    public function getDirectoryIterator()
    {
        $result = new \AppendIterator();

        foreach ($this->getDirs() as $dir) {
            if (\Includes\Utils\FileManager::isDir($dir)) {
                $result->append($this->getDirectorySPLIterator($dir));
            }
        }

        return $result;
    }

    /**
     * Return pack metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return parent::getMetadata() + array(
            self::METADATA_FIELD_ACTUAL_NAME        => $this->module->getActualName(),
            self::METADATA_FIELD_VERSION_MAJOR      => $this->module->callModuleMethod('getMajorVersion'),
            self::METADATA_FIELD_VERSION_MINOR      => $this->module->callModuleMethod('getMinorVersion'),
            self::METADATA_FIELD_VERSION_BUILD      => $this->module->callModuleMethod('getBuildVersion'),
            self::METADATA_FIELD_MIN_CORE_VERSION   => $this->module->callModuleMethod('getMinorRequiredCoreVersion'),
            self::METADATA_FIELD_NAME               => $this->module->callModuleMethod('getModuleName'),
            self::METADATA_FIELD_AUTHOR             => $this->module->callModuleMethod('getAuthorName'),
            self::METADATA_FIELD_ICON_LINK          => $this->module->callModuleMethod('getIconURL'),
            self::METADATA_FIELD_DESCRIPTION        => $this->module->callModuleMethod('getDescription'),
            self::METADATA_FIELD_DEPENDENCIES       => $this->module->callModuleMethod('getDependencies'),
            self::METADATA_FIELD_IS_SYSTEM          => $this->module->callModuleMethod('isSystem'),
        );
    }

    // }}}

    // {{{ Directories

    /**
     * Return list of module directories
     *
     * @return array
     */
    public function getDirs()
    {
        return array_merge($this->getClassDirs(), $this->getSkinDirs(), $this->getCustomSkinDirs());
    }

    /**
     * Helper to provide the skin prefix (see `getCustomSkinDirs` for more info)
     *
     * @param string $item
     *
     * @return void
     */
    public function addSkinPrefix(&$item)
    {
        $item = LC_DIR_SKINS . $item;
    }

    /**
     * Return list of module directories which contain class files
     *
     * @return array
     */
    protected function getClassDirs()
    {
        return array(
            \Includes\Utils\ModulesManager::getAbsoluteDir($this->module->getAuthor(), $this->module->getName())
        );
    }

    /**
     * Return list of module directories which contain templates
     *
     * @return array
     */
    protected function getSkinDirs()
    {
        $result = array();

        foreach (\XLite\Core\Layout::getInstance()->getSkinsAll() as $interface => $tmp) {
            $result = array_merge($result, \XLite\Core\Layout::getInstance()->getSkinPaths($interface, false, true, true));
        }

        $modulePath = \Includes\Utils\ModulesManager::getRelativeDir($this->module->getAuthor(), $this->module->getName());

        foreach ($result as $key => &$data) {
            $path = $data['fs'] . LC_DS . 'modules' . LC_DS . $modulePath;

            if (\Includes\Utils\FileManager::isDirReadable($path)) {
                $data = $path;

            } else {
                unset($result[$key]);
            }
        }

        return array_values(array_unique($result));
    }

    /**
     * Return list of module directories which contain templates. Custom skins
     *
     * @return array
     */
    protected function getCustomSkinDirs()
    {
        $result = array();

        // Collect the custom skins registered via the module
        foreach ($this->module->callModuleMethod('getSkins', array()) as $tmp) {
            $result = array_merge($result, $tmp);
        }

        array_walk($result, array($this, 'addSkinPrefix'));

        return array_values(array_unique($result));
    }

    /**
     * Return iterator for a directory
     *
     * @param string $dir Full directory path
     *
     * @return \RecursiveIteratorIterator
     */
    protected function getDirectorySPLIterator($dir)
    {
        return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS));
    }

    // }}}
}
