<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Logic\BulkEdit;

/**
 * Bulk edit generator
 */
class Generator extends \XLite\Base implements \SeekableIterator, \Countable
{
    /**
     * Default export process tick duration
     */
    const DEFAULT_TICK_DURATION = 0.5;

    /**
     * Options
     *
     * @var \ArrayObject
     */
    protected $options;

    /**
     * @var \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\AStep[]
     */
    protected $steps;

    /**
     * @var integer
     */
    protected $currentStep;

    /**
     * Generator instance
     *
     * @var static
     */
    protected static $instance;

    /**
     * Returns generator if it is initialised or FALSE otherwise
     *
     * @return static
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState(static::getEventName());

            static::$instance = ($state && isset($state['options']))
                ? new static($state['options'])
                : false;
        }

        return static::$instance;
    }

    /**
     * Run
     *
     * @param array $options Options
     */
    public static function run(array $options)
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(static::getBulkEditCancelFlagVarName(), false);
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->initializeEventState(
            static::getEventName(),
            ['options' => $options]
        );
        call_user_func(['XLite\Core\EventTask', static::getEventName()]);
    }

    /**
     * Cancel
     */
    public static function cancel()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(static::getBulkEditCancelFlagVarName(), true);
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState(static::getEventName());
    }

    /**
     * Constructor
     *
     * @param array $options Options OPTIONAL
     */
    public function __construct(array $options = [])
    {
        $this->options = [
                'data'      => isset($options['data']) ? $options['data'] : '',
                'scenarios' => isset($options['scenarios']) ? $options['scenarios'] : [],
                'filter'    => isset($options['filter']) ? $options['filter'] : '',
                'position'  => isset($options['position']) ? ((int) $options['position']) + 1 : 0,
                'errors'    => isset($options['errors']) ? $options['errors'] : [],
                'warnings'  => isset($options['warnings']) ? $options['warnings'] : [],
                'time'      => isset($options['time']) ? (int) $options['time'] : 0,
            ] + $options;

        $this->options = new \ArrayObject($this->options, \ArrayObject::ARRAY_AS_PROPS);
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
     * Finalize
     */
    public function finalize()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(
            static::getBulkEditTickDurationVarName(),
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
     * @return float
     */
    public function getTickDuration()
    {
        $result = null;
        if ($this->getOptions()->time && 1 < $this->getOptions()->position) {
            $result = $this->getOptions()->time / $this->getOptions()->position;

        } else {
            $tick = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')
                ->getVar(static::getBulkEditTickDurationVarName());
            if ($tick) {
                $result = $tick;
            }
        }

        return $result ? (ceil($result * 1000) / 1000) : static::DEFAULT_TICK_DURATION;
    }

    // {{{ Steps

    /**
     * Get steps
     *
     * @return \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\AStep[]
     */
    public function getSteps()
    {
        if (null === $this->steps) {
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
     * @return \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\AStep
     */
    public function getStep($reset = false)
    {
        if (null === $this->currentStep || $reset) {
            $this->currentStep = $this->defineStep();
        }

        $steps = $this->getSteps();

        return null !== $this->currentStep && isset($steps[$this->currentStep]) ? $steps[$this->currentStep] : null;
    }

    /**
     * Define steps
     *
     * @return array
     */
    protected function defineSteps()
    {
        return [
            'XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\Product',
        ];
    }

    /**
     * Process steps
     */
    protected function processSteps()
    {
        if ($this->getOptions()->scenarios) {
            foreach ($this->steps as $i => $step) {
                if (!in_array($step, $this->getOptions()->scenarios, true)) {
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

        if (!\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar(static::getBulkEditCancelFlagVarName())) {
            $i = $this->getOptions()->position;
            foreach ($this->getSteps() as $n => $step) {
                if ($i < $step->count()) {
                    $currentStep = $n;
                    $step->seek($i);

                    break;
                }

                $i -= $step->count();
            }
        }

        return $currentStep;
    }

    public function initialize()
    {
        foreach ($this->getSteps() as $step) {
            $step->initialize();
        }
    }

    // }}}

    // {{{ SeekableIterator, Countable

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
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
     * @return \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\AStep
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
     */
    public function addError($title, $body)
    {
        $this->getOptions()->errors[] = [
            'title' => $title,
            'body'  => $body,
        ];
    }

    /**
     * Get registered errors
     *
     * @return array
     */
    public function getErrors()
    {
        return empty($this->getOptions()->errors) ? [] : $this->getOptions()->errors;
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
    public static function getBulkEditTickDurationVarName()
    {
        return 'bulkEditTickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getBulkEditCancelFlagVarName()
    {
        return 'bulkEditCancelFlag';
    }

    /**
     * Get export event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'bulkEdit';
    }

    // }}}
}
