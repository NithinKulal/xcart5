<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Export;

/**
 * Generator
 */
class Generator extends \XLite\Base implements \SeekableIterator, \Countable
{
    /**
     * Default export directory
     */
    const EXPORT_DIR = 'export';

    /**
     * Default export cycle length
     */
    const DEFAULT_CYCLE_LENGTH = 50;

    /**
     * Max. file size for archieving
     */
    const MAX_FILE_SIZE = 50000000;

    /**
     * Default export process tick duration
     */
    const DEFAUL_TICK_DURATION = 0.5;

    /**
     * Language code
     *
     * @var string
     */
    static protected $languageCode;

    /**
     * Options
     *
     * @var \ArrayObject
     */
    protected $options;

    /**
     * Steps (cache)
     *
     * @var array
     */
    protected $steps;

    /**
     * Current step index
     *
     * @var integer
     */
    protected $currentStep;

    /**
     * Count (cached)
     *
     * @var integer
     */
    protected $countCache;

    /**
     * Flag: is export in progress (true) or no (false)
     *
     * @var boolean
     */
    protected static $inProgress = false;

    /**
     * Set inProgress flag value
     *
     * @param boolean $value Value
     *
     * @return void
     */
    public function setInProgress($value)
    {
        static::$inProgress = $value;
    }

    /**
     * Constructor
     *
     * @param array $options Options OPTIONAL
     *
     * @return void
     */
    public function __construct(array $options = array())
    {
        $delimiter = isset($options['delimiter']) ? $options['delimiter'] : ',';
        if ('tab' == $delimiter) {
            $delimiter = "\t";
        }

        $this->options = array(
            'position'  => isset($options['position']) ? intval($options['position']) + 1 : 0,
            'include'   => isset($options['include']) ? $options['include'] : array(),
            'charset'   => isset($options['charset']) ? $options['charset'] : 'UTF-8',
            'delimiter' => $delimiter,
            'enclosure' => isset($options['enclosure']) ? $options['enclosure'] : '"',
            'errors'    => isset($options['errors']) ? $options['errors'] : array(),
            'warnings'  => isset($options['warnings']) ? $options['warnings'] : array(),
            'dir'       => isset($options['dir']) ? $options['dir'] : static::EXPORT_DIR,
            'copyResources' => isset($options['copyResources']) ? $options['copyResources'] : true,
            'attrs'     => isset($options['attrs']) ? $options['attrs'] : 'all',
            'time'      => isset($options['time']) ? intval($options['time']) : 0,
            'selection'   => isset($options['selection']) ? $options['selection'] : array(),
            'filter'      => isset($options['filter']) ? $options['filter'] : '',
            'isAttrHeaderBuilt' => isset($options['isAttrHeaderBuilt']) ? (bool)$options['isAttrHeaderBuilt'] : false,
            'itemsList' => isset($options['itemsList']) ? $options['itemsList'] : false,
        ) + $options;

        static::$languageCode = isset($options['languageCode'])
            ? $options['languageCode']
            : \XLite\Core\Config::getInstance()->General->default_admin_language;

        $this->options = new \ArrayObject($this->options, \ArrayObject::ARRAY_AS_PROPS);

        if (0 == $this->getOptions()->position) {
            $this->initialize();
        }
    }

    /**
     * Get language code
     *
     * @return string
     */
    public static function getLanguageCode()
    {
        return static::$inProgress ? static::$languageCode : null;
    }

    /**
     * Get options
     *
     * @return \ArrayObject
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Delete all files
     *
     * @return void
     */
    public function deleteAllFiles()
    {
        if (!\Includes\Utils\FileManager::isExists(LC_DIR_VAR . $this->getOptions()->dir)) {
            \Includes\Utils\FileManager::mkdir(LC_DIR_VAR . $this->getOptions()->dir);
        }

        $list = glob(LC_DIR_VAR . $this->getOptions()->dir . LC_DS . '*');
        if ($list) {
            foreach ($list as $path) {
                if (is_file($path)) {
                    \Includes\Utils\FileManager::deleteFile($path);

                } else {
                    \Includes\Utils\FileManager::unlinkRecursive($path);
                }
            }
        }
    }

    /**
     * Run
     *
     * @param array $options Options
     *
     * @return void
     */
    public static function run(array $options)
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(static::getExportCancelFlagVarName(), false);
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->initializeEventState(
            static::getEventName(),
            array('options' => $options)
        );
        call_user_func(array('\XLite\Core\EventTask', static::getEventName()));
    }

    /**
     * Cancel
     *
     * @return void
     */
    public static function cancel()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(static::getExportCancelFlagVarName(), true);
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState(static::getEventName());
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
        $this->deleteAllFiles();
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(
            static::getExportTickDurationVarName(),
            $this->count() ? round($this->getOptions()->time / $this->count(), 3) : 0
        );
    }

    /**
     * Get time remain
     *
     * @return integer
     */
    public function getTimeRemain()
    {
        return $this->getTickDuration() * ($this->count() - $this->getOptions()->position);
    }

    /**
     * Get export process tick duration
     *
     * @return integer
     */
    public function getTickDuration()
    {
        $result = null;
        if ($this->getOptions()->time && 1 < $this->getOptions()->position) {
            $result = $this->getOptions()->time / $this->getOptions()->position;

        } else {
            $tick = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(static::getExportTickDurationVarName());
            if ($tick) {
                $result = $tick;
            }
        }

        return $result ? (ceil($result * 1000) / 1000) : static::DEFAUL_TICK_DURATION;
    }

    // {{{ Steps

    /**
     * Get steps
     *
     * @return array
     */
    public function getSteps()
    {
        if (!isset($this->steps)) {
            $this->steps = $this->defineSteps();
            $this->processSteps();
        }

        return $this->steps;
    }

    /**
     * Get current step
     *
     * @param boolean $reset Reset flag (OPTIONAL)
     *
     * @return \XLite\Logic\Export\Step\AStep
     */
    public function getStep($reset = false)
    {
        if (!isset($this->currentStep) || $reset) {
            $this->currentStep = $this->defineStep();
        }

        $steps = $this->getSteps();

        return isset($this->currentStep) && isset($steps[$this->currentStep]) ? $steps[$this->currentStep] : null;
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return array(
            'XLite\Logic\Export\Step\Attributes',
            'XLite\Logic\Export\Step\Categories',
            'XLite\Logic\Export\Step\Products',
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueCheckbox',
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueSelect',
            'XLite\Logic\Export\Step\AttributeValues\AttributeValueText',
            'XLite\Logic\Export\Step\Users',
            'XLite\Logic\Export\Step\Orders',
        );
    }

    /**
     * Process steps
     *
     * @return void
     */
    protected function processSteps()
    {
        if ($this->getOptions()->include) {
            foreach ($this->steps as $i => $step) {
                if (!in_array($step, $this->getOptions()->include)) {
                    unset($this->steps[$i]);
                }
            }
        }

        foreach ($this->steps as $i => $step) {
            if (\XLite\Core\Operator::isClassExists($step)) {
                $this->steps[$i] = new $step($this);

            } else {
                unset($this->steps[$i]);
            }
        }

        $this->steps = array_values($this->steps);
    }

    /**
     * Define current step
     *
     * @return integer
     */
    protected function defineStep()
    {
        $currentStep = null;

        if (!\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(static::getExportCancelFlagVarName())) {
            $i = $this->getOptions()->position;
            foreach ($this->getSteps() as $n => $step) {
                if ($i < $step->count()) {
                    $currentStep = $n;
                    $step->seek($i);
                    break;

                } else {
                    $i -= $step->count();
                }
            }
        }

        return $currentStep;
    }

    // }}}

    // {{{ SeekableIterator, Countable

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
     *
     * @return void
     */
    public function seek($position)
    {
        if ($position < $this->count()) {
            $this->getOptions()->position = $position;
            $this->getStep(true);
        }
    }

    /**
     * \SeekableIterator::current
     *
     * @return \XLite\Logic\Export\Step\AStep
     */
    public function current()
    {
        return $this->getStep()->current();
    }

    /**
     * \SeekableIterator::key
     *
     * @return integer
     */
    public function key()
    {
        return $this->getOptions()->position;
    }

    /**
     * \SeekableIterator::next
     *
     * @return void
     */
    public function next()
    {
        $this->getOptions()->position++;
        $this->getStep()->next();
        if ($this->getStep()->key() >= $this->getStep()->count()) {
            $this->getStep(true);
        }
    }

    /**
     * \SeekableIterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
    }

    /**
     * \SeekableIterator::valid
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->getStep() && $this->getStep()->valid() && !$this->hasErrors();
    }

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $this->countCache = 0;
            foreach ($this->getSteps() as $step) {
                $this->countCache += $step->count();
            }
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Error / warning routines

    /**
     * Add error
     *
     * @param string $title Title
     * @param string $body  Body
     *
     * @return void
     */
    public function addError($title, $body)
    {
        $this->getOptions()->errors[] = array(
            'title' => $title,
            'body'  => $body,
        );
    }

    /**
     * Get registered errors
     *
     * @return array
     */
    public function getErrors()
    {
        return empty($this->getOptions()->errors) ? array() : $this->getOptions()->errors;
    }

    /**
     * Check - has registered errors or not
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return !empty($this->getOptions()->errors);
    }

    // }}}

    // {{{ Download files

    /**
     * Get downloadable files
     *
     * @return array
     */
    public function getDownloadableFiles()
    {
        $result = array();

        foreach ($this->getFileIterator() as $filePath => $fileObject) {
            if (is_file($filePath)) {
                $result[] = $filePath;
            }
        }

        return $result;
    }

    /**
     * Get file iterator
     *
     * @return \RecursiveIteratorIterator
     */
    protected function getFileIterator()
    {
        $dir = LC_DIR_VAR . $this->getOptions()->dir . LC_DS;

        if (!\Includes\Utils\FileManager::isExists($dir)) {
            \Includes\Utils\FileManager::mkdirRecursive($dir);
        }

        $dirIterator = new \RecursiveDirectoryIterator(
            $dir,
            \FilesystemIterator::KEY_AS_PATHNAME | \FilesystemIterator::CURRENT_AS_FILEINFO | \FilesystemIterator::SKIP_DOTS
        );

        return new \RecursiveIteratorIterator($dirIterator, \RecursiveIteratorIterator::SELF_FIRST);
    }

    // }}}

    // {{{ Pack files

    /**
     * Check - can pack files with specified archive type
     *
     * @param string $type Archive type
     *
     * @return boolean
     */
    public function canPackFiles($type)
    {
        return \XLite\Core\Archive::getInstance()->isTypeAvailable(strtolower(strval($type)));

    }

    /**
     * Pack export files
     *
     * @param string $type Archive type
     *
     * @return string
     */
    public function packFiles($type)
    {
        $files = $this->getPackedFiles();

        $path = LC_DIR_TMP . 'export-'. $this->getPackExportDate();

        return \XLite\Core\Archive::getInstance()
            ->pack($files, $path, strtolower(strval($type))) ? $path : false;
    }

    /**
     * Get last export date
     *
     * @return string
     */
    protected function getPackExportDate()
    {
        $list = $this->getPackedFiles();
        $file = $list ? new \SplFileInfo(current($list)) : null;
        $date = date('Y-m-d');

        if ($file) {
            $date = \XLite\Core\Converter::formatDate($file->getMTime(), '%Y-%m-%d');
        }

        return $date;

    }

    /**
     * Get allowed archives
     *
     * @return array
     */
    public function getAllowedArchives()
    {
        $result = $this->getAllowedArchiveTypes();

        if (in_array('tar', $result) && function_exists('gzcompress')) {
            $key = array_search('tar', $result);
            unset($result[$key]);
        }

        return array_values($result);
    }

    /**
     * Get allowed archive types
     *
     * @return array
     */
    protected function getAllowedArchiveTypes()
    {
        return \XLite\Core\Archive::getInstance()->getTypes();
    }

    /**
     * Get excluded files
     *
     * @return array
     */
    protected function getExcludedFiles()
    {
        return array(
            '.htaccess'
        );
    }

    /**
     * Check file is allowed to pack or not
     *
     * @param string $filePath File path
     *
     * @return boolean
     */
    protected function isAllowedToPack($filePath)
    {
        return filesize($filePath) < \XLite\Logic\Export\Generator::MAX_FILE_SIZE
            && !in_array(basename($filePath), $this->getExcludedFiles());
    }

    /**
     * Get packed Files
     *
     * @return array
     */
    public function getPackedFiles()
    {
        $result = array();

        foreach ($this->getFileIterator() as $filePath => $fileObject) {
            if ($this->isAllowedToPack($filePath)) {
                $result[] = $filePath;
            }
        }

        return $result;
    }

    // }}}

    // {{{ Service variable names

    /**
     * Get exportTickDuration TmpVar name
     *
     * @return string
     */
    public static function getExportTickDurationVarName()
    {
        return 'exportTickDuration';
    }

    /**
     * Get export cancel flag name
     *
     * @return string
     */
    public static function getExportCancelFlagVarName()
    {
        return 'exportCancelFlag';
    }

    /**
     * Get export event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'export';
    }

    /**
     * Get export lock key
     *
     * @return string
     */
    public static function getLockKey()
    {
        return static::getEventName();
    }

    /**
     * Lock export with file lock
     */
    public static function lockExport()
    {
        \XLite\Core\Lock\FileLock::getInstance()->setRunning(
            static::getLockKey()
        );
    }

    /**
     * Check if export is locked right now
     */
    public static function isLocked()
    {
        return \XLite\Core\Lock\FileLock::getInstance()->isRunning(
            static::getLockKey(),
            true
        );
    }

    /**
     * Unlock export
     *
     * @return string
     */
    public static function unlockExport()
    {
        \XLite\Core\Lock\FileLock::getInstance()->release(
            static::getLockKey()
        );
    }

    // }}}
}
