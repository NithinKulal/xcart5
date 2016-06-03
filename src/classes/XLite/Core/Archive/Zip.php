<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Archive;

/**
 * Zip
 */
class Zip extends \XLite\Core\Archive\AArchive
{
    /**
     * Get archiver code
     *
     * @return string
     */
    public function getCode()
    {
        return 'zip';
    }

    /**
     * Check - archiver is valid or not
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && class_exists('ZipArchive', false);
    }

    /**
     * Check - can upack specified file
     *
     * @param string $path Path
     *
     * @return boolean
     */
    public function canUpackFile($path)
    {
        return (bool)preg_match('/\.zip/Ss', $path);
    }

    /**
     * Pack files
     *
     * @param array  $files            Files
     * @param string &$destinationPath Destination path
     *
     * @return boolean
     */
    public function pack(array $files, &$destinationPath)
    {
        $destinationPath .= '.zip';

        $packer = new \ZipArchive;
        $packer->open($destinationPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        $commonDirectory = $this->getCommonDirectory($files);

        $len = strlen($commonDirectory) + 1;
        $dirs = array();
        foreach ($files as $file) {
            $localpath = substr($file, $len);
            if (is_file($file)) {
                $packer->addFile($file, $localpath);

            } elseif (!in_array($localpath, $dirs)) {
                $packer->addEmptyDir($localpath);
                $dirs[] = $localpath;
            }
        }
        $packer->close();

        return true;
    }

    /**
     * Unpack archive
     *
     * @param string  $path            Archive path
     * @param string  $destinationPath Destination path
     * @param boolean $safeMode        Safe mode OPTIONAL
     *
     * @return boolean
     */
    public function unpack($path, $destinationPath, $safeMode = false)
    {
        $result = false;

        $packer = new \ZipArchive;
        if ($packer->open($path)) {
            if ($safeMode) {
                $entries = array();

                for ($i = 0; $i < $packer->numFiles; $i++) {
                    $file = $packer->getNameIndex($i);
                    if ($this->isSafeFile($file)) {
                        $entries[] = $file;
                    }
                }

                if ($entries) {
                    $result = $packer->extractTo($destinationPath, $entries);
                }

            } else {
                $result = $packer->extractTo($destinationPath);
            }

            $packer->close();
        }

        return $result;
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
        $result = array();

        $packer = new \ZipArchive;
        if ($packer->open($path)) {
            for ($i = 0; $i < $packer->numFiles; $i++) {
                $result[] = $packer->getNameIndex($i);
            }
        }

        return $result;
    }

}

