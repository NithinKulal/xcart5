<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\RemoveData;


/**
 * Generator
 */
class Generator extends \XLite\Logic\AGenerator
{
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
     * @param $options \ArrayObject
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    // {{{ Steps

    /**
     * @return array
     */
    protected function getStepsList()
    {
        return array(
            'XLite\Logic\RemoveData\Step\Products',
            'XLite\Logic\RemoveData\Step\Categories',
            'XLite\Logic\RemoveData\Step\Orders',
            'XLite\Logic\RemoveData\Step\Customers',
        );
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        $steps = array();
        $options = $this->getOptions();
        if (isset($options['steps'])) {
            $requestedSteps = $options['steps'];

            if (is_array($requestedSteps)) {
                foreach ($this->getStepsList() as $step) {
                    $_step = explode('\\', $step);
                    $_step = array_pop($_step);
                    $_step = strtolower($_step);

                    if (in_array($_step, $requestedSteps)) {
                        $steps[] = $step;
                    }
                }
            }
        }

        return $steps;
    }

    // }}}

    // {{{ SeekableIterator, Countable

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

    // {{{ Service variable names

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getTickDurationVarName()
    {
        return 'removeDataTickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getCancelFlagVarName()
    {
        return 'removeDataCancelFlag';
    }

    /**
     * Get event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'removeData';
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
    public static function lockRemovingData()
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
    public static function unlockRemovingData()
    {
        \XLite\Core\Lock\FileLock::getInstance()->release(
            static::getLockKey()
        );
    }

    // }}}
}