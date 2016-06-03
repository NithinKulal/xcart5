<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Archive;

/**
 * Tbz
 */
class Tbz extends \XLite\Core\Archive\Base\Tar
{
    /**
     * Get archiver code
     *
     * @return string
     */
    public function getCode()
    {
        return 'tbz';
    }

    /**
     * Check - archiver is valid or not
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid() && extension_loaded('bz2');
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
        return (bool)preg_match('/\.tar\.bz2?|\.tbz/Ss', $path);
    }

    /**
     * Prepare destination path
     *
     * @param string &$destinationPath Destination path
     *
     * @return string
     */
    protected function prepareDestinationPath(&$destinationPath)
    {
        $destinationPath .= '.tbz';
    }

    /**
     * Create packer
     *
     * @param string $destinationPath Destination path
     *
     * @return \Archive_Tar
     */
    protected function createPacker($destinationPath)
    {
        return new \Archive_Tar($destinationPath, 'bz2');
    }

}

