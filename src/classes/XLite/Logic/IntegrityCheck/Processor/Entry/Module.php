<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\IntegrityCheck\Processor\Entry;

/**
 * Class Module
 */
class Module implements IEntry
{
    /**
     * @var \XLite\Core\Pack\Module
     */
    protected $modulePack;

    /**
     * @var \XLite\Model\Module
     */
    protected $module;

    /**
     * @var
     */
    protected $version;

    /**
     * @var array
     */
    protected $errors;

    /**
     * Module constructor
     *
     * @param $name
     * @param $version
     *
     * @throws \Exception
     */
    public function __construct($name, $version)
    {
        $this->version = $version;

        $this->module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneByModuleName($name);

        if (!$this->module) {
            throw new \Exception('Module ' . $name . ' not found');
        }

        if (!defined('LC_MODULE_CONTROL')) {
            define('LC_MODULE_CONTROL', true);
        }

        $this->modulePack = new \XLite\Core\Pack\Module($this->module);
    }

    /**
     * @return \AppendIterator
     */
    public function getRealFiles()
    {
        return $this->modulePack->getDirectoryIterator();   
    }

    /**
     * @return array
     */
    public function getHashes()
    {
        $cacheDriver = \XLite\Core\Database::getCacheDriver();

        $result = $cacheDriver->fetch($this->getCacheKey());

        if ($result === false) {
            $key = $this->module->getLicenseKey()
                ? $this->module->getLicenseKey()->getKeyValue()
                : null;

            $result = \XLite\Core\Marketplace::getInstance()->getAddonHash(
                $this->module->getMarketplaceID(),
                $key,
                $this->module->getIdentityData() ?: null
            );

            if ($result) {
                $cacheDriver->save($this->getCacheKey(), $result, 86400);
            } else {
                $this->errors[] = \XLite\Core\Marketplace::getInstance()->getLastErrorCode();
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return md5(serialize(
            [
                $this->module->getMarketplaceID(),
                $this->module->getIdentityData() ?: null
            ]
        ));
    }
}
