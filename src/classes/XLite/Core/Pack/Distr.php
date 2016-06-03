<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Pack;

/**
 * Distr
 */
class Distr extends \XLite\Core\Pack\APack
{
    /**
     * Field names in metadata
     */
    const METADATA_FIELD_VERSION_MINOR = 'VersionMinor';
    const METADATA_FIELD_VERSION_MAJOR = 'VersionMajor';
    const METADATA_FIELD_VERSION_BUILD = 'VersionBuild';

    /**
     * List of patterns which are not required in pack
     *
     * @var array
     */
    protected $exclude = array();

    /**
     * List of exception patterns
     *
     * @var array
     */
    protected $include = array();

    /**
     * Exclude pattern
     *
     * @var string
     */
    protected $excludePattern;

    /**
     * Include pattern
     *
     * @var string
     */
    protected $includePattern;

    // {{{ Public methods

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->exclude[] = 'var';
        $this->exclude[] = 'files';
        $this->exclude[] = 'images';
        $this->exclude[] = 'sql';
        $this->exclude[] = 'etc' . LC_DS . 'config.local.php';
        $this->exclude[] = 'etc' . LC_DS . 'config.personal.php';

        $this->include[] = 'var' . LC_DS . '.htaccess';
        $this->include[] = 'files' . LC_DS . '.htaccess';
        $this->include[] = 'images' . LC_DS . '.htaccess';
        $this->include[] = 'images' . LC_DS . 'spacer.gif';
        $this->include[] = 'sql' . LC_DS . 'xlite_data.yaml';
        $this->include[] = 'sql' . LC_DS . 'xlite_data_lng.yaml';
    }

    /**
     * Return pack name
     *
     * @return string
     */
    public function getName()
    {
        // It's the fix for PHAR::compress(): it's triming dots in file names
        return 'LC-Distr-v' . str_replace('.', '_', \XLite::getInstance()->getVersion());
    }

    /**
     * Return iterator to walk through directories
     *
     * @return \Iterator
     */
    public function getDirectoryIterator()
    {
        $result = new \Includes\Utils\FileFilter(LC_DIR_ROOT);
        $result = $result->getIterator();
        $this->preparePatterns();
        $result->registerCallback(array($this, 'filterCoreFiles'));

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
            self::METADATA_FIELD_VERSION_MAJOR => \XLite::getInstance()->getMajorVersion(),
            self::METADATA_FIELD_VERSION_MINOR => \XLite::getInstance()->getMinorOnlyVersion(),
            self::METADATA_FIELD_VERSION_BUILD => \XLite::getInstance()->getBuildVersion(),
        );
    }

    /**
     * Preapre patterns
     *
     * @return void
     */
    protected function preparePatterns()
    {
        $list = array();
        foreach ($this->exclude as $pattern) {
            $list[] = preg_quote($pattern, '/');
        }

        $this->excludePattern = '/^(?:' . implode('|', $list) . ')/Ss';

        $list = array();
        foreach ($this->include as $pattern) {
            $list[] = preg_quote($pattern, '/');
        }

        $this->includePattern = '/^(?:' . implode('|', $list) . ')/Ss';

    }

    // }}}

    // {{{ Auxiliary methods

    /**
     * Callback to filter files
     *
     * @param \Includes\Utils\FileFilter\FilterIterator $iterator Directory iterator
     *
     * @return boolean
     */
    public function filterCoreFiles(\Includes\Utils\FileFilter\FilterIterator $iterator)
    {
        // Relative path in LC root directory
        $path = \Includes\Utils\FileManager::getRelativePath($iterator->getPathname(), LC_DIR_ROOT);

        return !preg_match($this->excludePattern, $path)
            || preg_match($this->includePattern, $path);
    }

    // }}}
}
