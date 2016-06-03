<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Archive;

/**
 * Tar 
 */
class Tar extends \XLite\Core\Archive\Base\Tar
{
    /**
     * Get archiver code
     *
     * @return string
     */
    public function getCode()
    {
        return 'tar';
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
        return (bool)preg_match('/\.tar/Ss', $path);
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
        $destinationPath .= '.tar';
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
        return new \Archive_Tar($destinationPath);
    }

}

