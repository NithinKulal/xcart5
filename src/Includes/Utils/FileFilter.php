<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils;

/**
 * FileFilter
 *
 * @package    XLite
 */
class FileFilter extends \Includes\Utils\AUtils
{
    /**
     * Directory to iterate over
     *
     * @var string
     */
    protected $dir;

    /**
     * Pattern to filter files by path
     *
     * @var string
     */
    protected $pattern;

    /**
     * Mode
     *
     * @var int
     */
    protected $mode;

    /**
     * Cache
     *
     * @var \Includes\Utils\FileFilter\FilterIterator
     */
    protected $iterator;


    /**
     * Return the directory iterator
     *
     * @return \RecursiveIteratorIterator
     */
    protected function getUnfilteredIterator()
    {
        return new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->dir, \FilesystemIterator::SKIP_DOTS),
            $this->mode
        );
    }


    /**
     * Return the directory iterator
     *
     * @return \Includes\Utils\FileFilter\FilterIterator
     */
    public function getIterator()
    {
        if (!isset($this->iterator)) {
            $this->iterator = new \Includes\Utils\FileFilter\FilterIterator(static::getUnfilteredIterator(), $this->pattern);
        }

        return $this->iterator;
    }

    /**
     * Constructor
     *
     * @param string $dir     Directory to iterate over
     * @param string $pattern Pattern to filter files
     * @param int    $mode    Filtering mode OPTIONAL
     *
     * @return void
     */
    public function __construct($dir, $pattern = null, $mode = \RecursiveIteratorIterator::LEAVES_ONLY)
    {
        $canonicalDir = \Includes\Utils\FileManager::getCanonicalDir($dir);

        if (empty($canonicalDir)) {
            \Includes\ErrorHandler::fireError('Path "' . $dir . '" is not exists or is not readable.');
        }

        $this->dir     = $canonicalDir;
        $this->pattern = $pattern;
        $this->mode    = $mode;
    }
}
