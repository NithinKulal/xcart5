<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Upgrade\Entry\Module;

/**
 * Uploaded
 */
class Uploaded extends \XLite\Upgrade\Entry\Module\AModule
{
    /**
     * Default URL for module icon
     */
    const DEFAULT_ICON_URL = 'skins/admin/images/addon_default.png';

    /**
     * Module metadata
     *
     * @var array
     */
    protected $metadata;

    /**
     * Return module actual name
     *
     * @return string
     */
    public function getActualName()
    {
        return $this->getMetadata('ActualName');
    }

    /**
     * Return entry readable name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getMetadata('Name');
    }

    /**
     * Return icon URL
     *
     * @return string
     */
    public function getIconURL()
    {
        $url = $this->getMetadata('IconLink');
        if (!$url) {
            list($author, $name) = explode('\\', $this->getActualName());
            $path = \Includes\Utils\ModulesManager::getModuleIconFile($author, $name);
            if (\Includes\Utils\FileManager::isFileReadable($path)) {
                $url = \XLite\Core\Converter::buildURL(
                    'module',
                    null,
                    array(
                        'author' => $author,
                        'name'   => $name,
                    ),
                    'image.php'
                );
            }
        }

        return $url ?: static::DEFAULT_ICON_URL;
    }

    /**
     * Return entry old major version
     *
     * @return string
     */
    public function getMajorVersionOld()
    {
        return $this->callModuleMethod('getMajorVersion');
    }

    /**
     * Return entry old minor version
     *
     * @return string
     */
    public function getMinorVersionOld()
    {
        return $this->callModuleMethod('getFullMinorVersion');
    }

    /**
     * Return entry new major version
     *
     * @return string
     */
    public function getMajorVersionNew()
    {
        return $this->getMetadata('VersionMajor');
    }

    /**
     * Return entry new minor version
     *
     * @return string
     */
    public function getMinorVersionNew()
    {
        return $this->getMetadata('VersionMinor')
            . ($this->getMetadata('VersionBuild') ? '.' . $this->getMetadata('VersionBuild') : '');
    }

    /**
     * Return entry revision date
     *
     * @return integer
     */
    public function getRevisionDate()
    {
        return $this->getMetadata('RevisionDate');
    }

    /**
     * Return module author readable name
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->getMetadata('Author');
    }

    /**
     * Return module description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getMetadata('Description');
    }

    /**
     * Check if module is enabled
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->isInstalled();
    }

    /**
     * Check if module is installed
     *
     * @return boolean
     */
    public function isInstalled()
    {
        return \Includes\Utils\ModulesManager::isModuleInstalled($this->getActualName());
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
     * Return entry pack size
     *
     * @return integer
     */
    public function getPackSize()
    {
        return intval(\Includes\Utils\FileManager::getFileSize($this->getRepositoryPath(), true));
    }

    /**
     * Return module dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return $this->getMetadata('Dependencies');
    }

    /**
     * Unpack archive
     *
     * @return boolean
     */
    public function unpack()
    {
        parent::unpack();
        $this->saveHashesForInstalledFiles();

        return $this->isUnpacked();
    }

    /**
     * Calculate hashes for current version
     *
     * @return array
     */
    protected function loadHashesForInstalledFiles()
    {
        $result = array();
        $module = $this->getModuleOldInstalled();

        if ($module) {
            $pack = new \XLite\Core\Pack\Module($module);

            foreach ($pack->getDirectoryIterator() as $file) {

                if ($file->isFile()) {
                    $relativePath = \Includes\Utils\FileManager::getRelativePath($file->getPathname(), LC_DIR_ROOT);

                    if ($relativePath) {
                        $result[$relativePath] = \Includes\Utils\FileManager::getHash($file->getPathname(), true);
                    }
                }
            }
        }

        return $result ?: $this->getHashes(true);
    }

    /**
     * @param boolean $checkForErrors
     *
     * @return boolean
     */
    public function isValid($checkForErrors = true)
    {
        return parent::isValid($checkForErrors)
            && \Includes\Utils\FileManager::isReadable($this->getRepositoryPath())
            && !!$this->metadata;
    }

    /**
     * Overloaded constructor
     *
     * @param string $path Path to the module package
     *
     * @return void
     */
    public function __construct($path)
    {
        if (!\Includes\Utils\FileManager::isFileReadable($path)) {
            \Includes\ErrorHandler::fireError('Unable to read module package: "' . $path . '"');
        }

        $this->setRepositoryPath($path);

        $module = new \PharData($this->getRepositoryPath());
        $this->metadata = $module->getMetaData();

        if (empty($this->metadata)) {
            \Includes\ErrorHandler::fireError('Unable to read module metadata: "' . $path . '"');
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
        $list[] = 'metadata';

        return $list;
    }

    /**
     * Get module metadata (or only the certain field from it)
     *
     * @param string $name Array index
     *
     * @return mixed
     */
    protected function getMetadata($name)
    {
        return \Includes\Utils\ArrayManager::getIndex($this->metadata, $name, true);
    }

    /**
     * Method to access module main clas methods
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
     * Find installed module
     *
     * @return \XLite\Model\Module
     */
    protected function getModuleInstalled()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Module')->findOneBy($this->getModuleData());
    }

    /**
     * Find installed module old version
     *
     * @return \XLite\Model\Module
     */
    protected function getModuleOldInstalled()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Module')->findOneBy($this->getModuleDataInstalled());
    }

    protected function parseMinorVersion($version)
    {

    }

    /**
     * Return common module data
     *
     * @return array
     */
    protected function getModuleData()
    {
        list($author, $name) = explode('\\', $this->getActualName());

        list($minorVersion, $build) = \Includes\Utils\Converter::parseMinorVersion($this->getMinorVersionNew());

        return array(
            'name'            => $name,
            'author'          => $author,
            'majorVersion'    => $this->getMajorVersionNew(),
            'minorVersion'    => $minorVersion,
            'build'           => $build,
            'fromMarketplace' => false,
            'installed'       => true,
            'authorEmail'     => '',
        );
    }

    /**
     * Return installed module common data
     *
     * @return array
     */
    protected function getModuleDataInstalled()
    {
        list($minorVersion, $build) = \Includes\Utils\Converter::parseMinorVersion($this->getMinorVersionOld());

        $data = $this->getModuleData();
        $data['majorVersion'] = $this->getMajorVersionOld();
        $data['minorVersion'] = $minorVersion;
        $data['build']        = $build;

        return $data;
    }

    /**
     * Update database records
     *
     * @return array
     */
    protected function updateDBRecords()
    {
        $this->setIsFreshInstall(!$this->isInstalled());

        $module = $this->getModuleInstalled() ?: new \XLite\Model\Module($this->getModuleData());

        $module->setDate(\XLite\Core\Converter::time());
        $module->setRevisionDate($this->getRevisionDate());
        $module->setPackSize($this->getPackSize());
        $module->setModuleName($this->getName());
        $module->setAuthorName($this->getAuthor());
        $module->setDescription($this->getDescription());
        $module->setIconURL($this->getIconURL());
        $module->setDependencies($this->getDependencies());

        $data = $this->getModuleData();
        unset($data['majorVersion']);
        unset($data['minorVersion']);

        list($minorVersionNew, $build) = \Includes\Utils\Converter::parseMinorVersion($this->getMinorVersionNew());

        $modules = \XLite\Core\Database::getRepo('\XLite\Model\Module')->findBy($data);

        foreach ($modules as $moduleOld) {
            if ($moduleOld) {
                if ($moduleOld->getMajorVersion() != $this->getMajorVersionNew()
                    || $moduleOld->getMinorVersion() != $minorVersionNew
                    || $moduleOld->getBuild() != $build
                ) {
                    // Old module has different version - delete it
                    $module->setYamlLoaded($moduleOld->getYamlLoaded());
                    \XLite\Core\Database::getRepo('\XLite\Model\Module')->delete($moduleOld);
                }
            }
        }

        // Save changes in DB
        if ($module->getModuleID()) {
            $module->setMajorVersion($this->getMajorVersionNew());
            $module->setMinorVersion($minorVersionNew);
            $module->setBuild($build);
            \XLite\Core\Database::getRepo('\XLite\Model\Module')->update($module);

        } else {
            $module->setEnabled(!$module->isSkinModule());
            $module->setIsSkin($module->isSkinModule());
            \XLite\Core\Database::getRepo('\XLite\Model\Module')->insert($module);
        }

        \XLite\Controller\Admin\Base\AddonsList::storeRecentlyInstalledModules(array($module->getModuleID()));
    }
}
