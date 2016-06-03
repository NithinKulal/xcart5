<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Sales;

use XLite\Core;

/**
 * Sales generator
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
     * Run
     *
     * @param array $options Options
     *
     * @return void
     */
    public static function run(array $options)
    {
        /** @var \XLite\Model\Repo\TmpVar $repo */
        $repo = Core\Database::getRepo('XLite\Model\TmpVar');
        $repo->setVar(static::getSalesCancelFlagVarName(), false);
        $repo->initializeEventState(
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
        /** @var \XLite\Model\Repo\TmpVar $repo */
        $repo = Core\Database::getRepo('XLite\Model\TmpVar');
        $repo->setVar(static::getSalesCancelFlagVarName(), true);
        $repo->removeEventState(static::getEventName());
    }

    /**
     * Constructor
     *
     * @param array $options Options OPTIONAL
     */
    public function __construct(array $options = array())
    {
        $this->options = array(
                'include'   => isset($options['include']) ? $options['include'] : array(),
                'position'  => isset($options['position']) ? ((int) $options['position']) + 1 : 0,
                'errors'    => isset($options['errors']) ? $options['errors'] : array(),
                'warnings'  => isset($options['warnings']) ? $options['warnings'] : array(),
                'time'      => isset($options['time']) ? ((int) $options['time']) : 0,
            ) + $options;

        $this->options = new \ArrayObject($this->options, \ArrayObject::ARRAY_AS_PROPS);

        if (0 === (int) $this->getOptions()->position) {
            $this->initialize();
        }
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
     *
     * @return void
     */
    public function finalize()
    {
        /** @var \XLite\Model\Repo\TmpVar $repo */
        $repo = Core\Database::getRepo('XLite\Model\TmpVar');
        $repo->setVar(
            static::getSalesTickDurationVarName(),
            $this->count() ? round($this->getOptions()->time / $this->count(), 3) : 0
        );

        foreach ($this->getSteps() as $step) {
            $step->finalize();
        }
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
            /** @var \XLite\Model\Repo\TmpVar $repo */
            $repo = Core\Database::getRepo('XLite\Model\TmpVar');
            $tick = $repo->getVar(static::getSalesTickDurationVarName());
            if ($tick) {
                $result = $tick;
            }
        }

        return $result ? (ceil($result * 1000) / 1000) : static::DEFAULT_TICK_DURATION;
    }

    /**
     * Initialize
     *
     * @return void
     */
    protected function initialize()
    {
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
            'XLite\Logic\Sales\Step\Products',
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
                if (!in_array($step, $this->getOptions()->include, true)) {
                    unset($this->steps[$i]);
                }
            }
        }

        foreach ($this->steps as $i => $step) {
            if (Core\Operator::isClassExists($step)) {
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
        /** @var \XLite\Model\Repo\TmpVar $repo */
        $repo = Core\Database::getRepo('XLite\Model\TmpVar');

        if (!$repo->getVar(static::getSalesCancelFlagVarName())) {
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

    // {{{ Service variable names

    /**
     * Get resizeTickDuration TmpVar name
     *
     * @return string
     */
    public static function getSalesTickDurationVarName()
    {
        return 'salesTickDuration';
    }

    /**
     * Get resize cancel flag name
     *
     * @return string
     */
    public static function getSalesCancelFlagVarName()
    {
        return 'salesCancelFlag';
    }

    /**
     * Get export event name
     *
     * @return string
     */
    public static function getEventName()
    {
        return 'sales';
    }

    // }}}
}
