<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Archive
 */
class Archive extends \XLite\Base\Singleton
{
    /**
     * Archivers list
     *
     * @var   array
     */
    protected $archivers;

    /**
     * Pack
     *
     * @param array  $files            Files list
     * @param string &$destinationPath Archive path
     * @param string $code             Archiver code OPTIONAL
     *
     * @return boolean
     */
    public function pack(array $files, &$destinationPath, $code = null)
    {
        $archiver = $code
            ? $this->getArchiverByCode($code)
            : $this->getDefaultArchiver();

        return $archiver ? $archiver->pack($files, $destinationPath) : false;
    }

    /**
     * Unpack
     *
     * @param string  $path            Archive path
     * @param string  $destinationPath Destination path
     * @param boolean $safeMode        Safe mode OPTIONAL
     *
     * @return boolean
     */
    public function unpack($path, $destinationPath, $safeMode = false)
    {
        $archiver = $this->getArchiverByPath($path);

        return $archiver ? $archiver->unpack($path, $destinationPath, $safeMode) : false;
    }

    /**
     * Get archive files listing
     *
     * @param string $path Archive path
     *
     * @return array
     */
    public function getList($path)
    {
        $archiver = $this->getArchiverByPath($path);

        return $archiver ? $archiver->getList($path) : false;
    }

    /**
     * Check - specified file is archive or not
     *
     * @param string $path File path
     *
     * @return boolean
     */
    public function isArchive($path)
    {
        return (bool)$this->getArchiverByPath($path);
    }

    /**
     * Check - specified arhiver is available or not
     *
     * @return boolean
     */
    public function isTypeAvailable($code)
    {
        return in_array($code, $this->getTypes());
    }

    /**
     * Get types
     *
     * @return array
     */
    public function getTypes()
    {
        $result = array();

        foreach ($this->getArchivers() as $archiver) {
            $result[] = $archiver->getCode();
        }

        return $result;
    }

    // {{{ Archivers

    /**
     * Geta archiver by code
     *
     * @param string $code Archiver code
     *
     * @return \XLite\Core\Archive\AArchive
     */
    protected function getArchiverByCode($code)
    {
        $result = null;

        foreach ($this->getArchivers() as $archiver) {
            if ($archiver->getCode() == $code) {
                $result = $archiver;
                break;
            }
        }

        return $result;
    }

    /**
     * Get archiver by path
     *
     * @param string $path Archive path
     *
     * @return \XLite\Core\Archive\AArchive
     */
    protected function getArchiverByPath($path)
    {
        $result = null;

        foreach ($this->getArchivers() as $archiver) {
            if ($archiver->canUpackFile($path)) {
                $result = $archiver;
                break;
            }
        }

        return $result;
    }

    /**
     * Get default archiver
     *
     * @return \XLite\Core\Archive\AArchive
     */
    protected function getDefaultArchiver()
    {
        $list = $this->getArchivers();

        return current($list);
    }

    /**
     * Get archivers list
     *
     * @return array
     */
    protected function getArchivers()
    {
        if (!isset($this->archivers)) {
            $this->archivers = $this->defineArchivers();
            $this->prepareArchivers();
        }

        return $this->archivers;
    }

    /**
     * Define archivers classes list
     *
     * @return array
     */
    protected function defineArchivers()
    {
        return array(
            'XLite\Core\Archive\Tar',
            'XLite\Core\Archive\Tgz',
            'XLite\Core\Archive\Tbz',
            'XLite\Core\Archive\Zip',
        );
    }

    /**
     * Prepare archivers
     *
     * @return void
     */
    protected function prepareArchivers()
    {
        foreach ($this->archivers as $i => $archiver) {
            $archiver = new $archiver();
            if ($archiver->isValid()) {
                $this->archivers[$i] = $archiver;

            } else {
                unset($this->archivers[$i]);
            }
        }

        $this->archivers = array_values($this->archivers);
    }

    // }}}
}

