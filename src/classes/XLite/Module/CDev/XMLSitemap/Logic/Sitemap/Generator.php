<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Logic\Sitemap;

use Includes\Utils\FileManager;
use XLite\Core\Database;
use XLite\Core\Lock\FileLock;

/**
 * Generator
 */
class Generator extends \XLite\Base implements \SeekableIterator, \Countable
{
    /**
     * Default process tick duration
     */
    const DEFAULT_TICK_DURATION = 0.5;

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
     * Record
     *
     * @var string
     */
    protected $record;

    /**
     * Flag: is process in progress (true) or no (false)
     *
     * @var boolean
     */
    protected static $inProgress = false;

    /**
     * Is page has alternative language url
     *
     * @var boolean
     */
    protected $hasAlternateLangUrls;

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
     * Run
     *
     * @param array $options Options
     *
     * @return void
     */
    public static function run(array $options)
    {
        Database::getRepo('XLite\Model\TmpVar')->setVar(static::getSitemapGenerationCancelFlagVarName(), false);
        Database::getRepo('XLite\Model\TmpVar')->initializeEventState(
            static::getEventName(),
            array('options' => $options)
        );
        call_user_func(array('XLite\Core\EventTask', static::getEventName()));
    }

    /**
     * Cancel
     *
     * @return void
     */
    public static function cancel()
    {
        Database::getRepo('XLite\Model\TmpVar')->setVar(static::getSitemapGenerationCancelFlagVarName(), true);
        Database::getRepo('XLite\Model\TmpVar')->removeEventState(static::getEventName());
    }

    /**
     * Constructor
     *
     * @param array $options Options OPTIONAL
     */
    public function __construct(array $options = array())
    {
        $this->options = array(
                'include' => isset($options['include']) ? $options['include'] : array(),
                'position' => isset($options['position']) ? intval($options['position']) + 1 : 0,
                'errors' => isset($options['errors']) ? $options['errors'] : array(),
                'warnings' => isset($options['warnings']) ? $options['warnings'] : array(),
                'time' => isset($options['time']) ? intval($options['time']) : 0,
            ) + $options;

        $this->options = new \ArrayObject($this->options, \ArrayObject::ARRAY_AS_PROPS);

        if (0 == $this->getOptions()->position) {
            $this->initialize();
        }
    }

    /**
     * Check if store has alternative language url
     *
     * @return bool
     */
    public function hasAlternateLangUrls()
    {
        if (null === $this->hasAlternateLangUrls) {
            $router = \XLite\Core\Router::getInstance();
            $this->hasAlternateLangUrls = LC_USE_CLEAN_URLS
                && $router->isUseLanguageUrls()
                && count($router->getActiveLanguagesCodes()) > 1;
        }

        return $this->hasAlternateLangUrls;
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
     * @param $options \ArrayObject
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        Database::getRepo('XLite\Model\TmpVar')->setVar(
            static::getSitemapGenerationTickDurationVarName(),
            $this->count() ? round($this->getOptions()->time / $this->count(), 3) : 0
        );

        foreach ($this->getSteps() as $step) {
            $step->finalize();
        }

        $this->setRecord($this->getRecord() . $this->getFooter());
        $this->flushRecord();
        $this->removeSitemapFiles();
        $this->moveSitemaps();
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
     * Get process tick duration
     *
     * @return float
     */
    public function getTickDuration()
    {
        $result = null;
        if ($this->getOptions()->time && 1 < $this->getOptions()->position) {
            $result = $this->getOptions()->time / $this->getOptions()->position;

        } else {
            $tick = Database::getRepo('XLite\Model\TmpVar')
                ->getVar(static::getSitemapGenerationTickDurationVarName());
            if ($tick) {
                $result = $tick;
            }
        }

        return $result ?: static::DEFAULT_TICK_DURATION;
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
        if (!FileManager::isExists(LC_DIR_DATA)) {
            FileManager::mkdir(LC_DIR_DATA);
            if (!FileManager::isExists(LC_DIR_DATA)) {
                $message = 'The directory ' . LC_DIR_DATA . ' can not be created. Check the permissions to create directories.';

                \XLite\Logger::getInstance()->log($message, LOG_ERR);

                $this->addError('Directory permissions', $message);

                $this->cancel();
            }
        }

        $this->removeTemporarySitemapFiles();
        $this->setFileIndex(1);
        $this->setRecord($this->getHead());
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
     * @param boolean $reset Reset flag OPTIONAL
     *
     * @return \SeekableIterator|\Countable both of interfaces
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
     * Return steps list
     *
     * @return array
     */
    protected function getStepsList()
    {
        return [
            'XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Step\Welcome',
            'XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Step\Categories',
            'XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Step\Products',
        ];
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return $this->getStepsList();
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

        $steps = $this->steps;
        $this->steps = [];
        foreach ($steps as $step) {
            if (\XLite\Core\Operator::isClassExists($step)) {
                $this->steps[] = new $step($this);

                if ($this->hasAlternateLangUrls()) {
                    foreach (\XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages() as $language) {
                        $this->steps[] = new $step($this, $language->getCode());
                    }
                }
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

        if (!Database::getRepo('XLite\Model\TmpVar')->getVar(static::getSitemapGenerationCancelFlagVarName())) {
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
     * @return \SeekableIterator|\Countable both of interfaces
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
            if (!isset($this->options['count'])) {
                $this->options['count'] = 0;
                foreach ($this->getSteps() as $step) {
                    $this->options['count'] += $step->count();
                    $this->options['count' . get_class($step)] = $step->count();
                }
            }
            $this->countCache = $this->options['count'];
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
            'body' => $body,
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

    // {{{ Service variable names

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getSitemapGenerationTickDurationVarName()
    {
        return 'sitemapGenerationTickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getSitemapGenerationCancelFlagVarName()
    {
        return 'sitemapGenerationCancelFlag';
    }

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'sitemapGeneration';
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
    public static function lockSitemapGeneration()
    {
        FileLock::getInstance()->setRunning(
            static::getLockKey()
        );
    }

    /**
     * Check if export is locked right now
     *
     * @return boolean
     */
    public static function isLocked()
    {
        return FileLock::getInstance()->isRunning(
            static::getLockKey(),
            true
        );
    }

    /**
     * Unlock export
     *
     * @return string
     */
    public static function unlockSitemapGeneration()
    {
        FileLock::getInstance()->release(
            static::getLockKey()
        );
    }

    // }}}


    // {{{ File operations

    /**
     * Return head
     *
     * @return string
     */
    protected function getHead()
    {
        return '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL
        . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . PHP_EOL;
    }

    /**
     * Return footer
     *
     * @return string
     */
    protected function getFooter()
    {
        return '</urlset>';
    }

    /**
     * Return current file index
     *
     * @return int
     */
    protected function getFileIndex()
    {
        return $this->getOptions()->fileIndex;
    }

    /**
     * Set current file index
     *
     * @param integer $index
     */
    protected function setFileIndex($index)
    {
        $this->getOptions()->fileIndex = $index;
    }

    /**
     * Get file prefix for generated sitemaps
     *
     * @return string
     */
    protected static function getPrefix()
    {
        return 'tmp_';
    }

    /**
     * Return array of temporary files
     *
     * @return array
     */
    protected function getTemporarySitemapFiles()
    {
        return glob(LC_DIR_DATA . static::getPrefix() . 'xmlsitemap.*.xml');
    }

    /**
     * Remove temporary files
     */
    protected function removeTemporarySitemapFiles()
    {
        foreach ($this->getTemporarySitemapFiles() as $path) {
            FileManager::deleteFile($path);
        }
    }

    /**
     * Return array of previously generated sitemap files
     *
     * @return array
     */
    protected function getSitemapFiles()
    {
        return glob(LC_DIR_DATA . 'xmlsitemap.*.xml');
    }

    /**
     * Remove temporary files
     */
    protected function removeSitemapFiles()
    {
        foreach ($this->getSitemapFiles() as $path) {
            FileManager::deleteFile($path);
        }
    }

    /**
     * Move sitemap files
     *
     * @return void
     */
    public function moveSitemaps()
    {
        $sep = preg_quote(LC_DS, '/');
        $prefix = preg_quote($this->getPrefix(), '/');
        foreach ($this->getTemporarySitemapFiles() as $path) {
            $to = preg_replace('/^(.+' . $sep . ')' . $prefix . '(xmlsitemap\..*\.xml)$/', '\\1\\2', $path);
            FileManager::move($path, $to);
        }
    }

    /**
     * Get sitemap path
     *
     * @return string
     */
    protected function getCurrentTemporarySitemapPath()
    {
        return LC_DIR_DATA . static::getPrefix() . 'xmlsitemap.' . $this->getFileIndex() . '.xml';
    }

    /**
     * Return Record
     *
     * @return string
     */
    public function getRecord()
    {
        return $this->record;
    }

    /**
     * Set Record
     *
     * @param string $record
     *
     * @return $this
     */
    public function setRecord($record)
    {
        $this->record = $record;
        return $this;
    }

    /**
     * Build location URL
     *
     * @param array $loc Locationb as array
     *
     * @return string
     */
    protected function buildLoc(array $loc)
    {
        $target = $loc['target'];
        unset($loc['target']);

        return \XLite\Core\Converter::buildFullURL($target, '', $loc, \XLite::getCustomerScript(), true);
    }

    /**
     * Add sitemap item to record
     *
     * @param array $item
     *
     * @return $this
     */
    public function addToRecord(array $item)
    {
        $time = $item['lastmod'];
        $item['lastmod'] = date('Y-m-d', $time) . 'T' . date('H:i:s', $time) . 'Z';

        $string = '<url>';
        foreach ($item as $tag => $value) {
            if (!empty($value)) {
                $string .= '<' . $tag . '>' . htmlentities($value) . '</' . $tag . '>';
            } else {
                $string .= '<' . $tag . ' />';
            }
        }
        $string .= '</url>';

        $this->setRecord($this->getRecord() . $string);

        return $this;
    }

    /**
     * Write record
     *
     * @return void
     */
    public function flushRecord()
    {
        if ($this->needSwitch()) {
            FileManager::write($this->getCurrentTemporarySitemapPath(), $this->getFooter(), FILE_APPEND);
            $this->setFileIndex($this->getFileIndex() + 1);
            $this->setRecord($this->getHead() . $this->getRecord());
        }

        if ($this->getRecord()) {
            FileManager::write($this->getCurrentTemporarySitemapPath(), $this->getRecord(), FILE_APPEND);
            $this->setRecord('');
        }
    }

    /**
     * Check - need switch to next file or not
     *
     * @return boolean
     */
    protected function needSwitch()
    {
        $path = $this->getCurrentTemporarySitemapPath();
        return file_exists($path) && 5242880 < filesize($path);
    }

    // }}}
}