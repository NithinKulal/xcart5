<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Step;

/**
 * Abstract import step
 */
abstract class AStep extends \XLite\Base implements \SeekableIterator, \Countable
{
    /**
     * Default import process tick duration
     */
    const DEFAUL_TICK_DURATION = 0.5;

    /**
     * Importer (cache)
     *
     * @var   \XLite\Logic\Import\Importer
     */
    protected $importer;

    /**
     * Last position (cache)
     *
     * @var integer
     */
    protected $lastPosition = 0;

    /**
     * Step index
     *
     * @var integer
     */
    protected $index;

    /**
     * Process row
     *
     * @return boolean
     */
    abstract public function process();

    /**
     * \Counable::count
     *
     * @return integer
     */
    abstract public function count();

    /**
     * Get final note
     *
     * @return string
     */
    abstract public function getFinalNote();

    /**
     * Get note
     *
     * @return string
     */
    abstract public function getNote();

    /**
     * Get error language label
     *
     * @return array
     */
    abstract public function getErrorLanguageLabel();

    /**
     * Get normal language label
     *
     * @return array
     */
    abstract public function getNormalLanguageLabel();

    /**
     * Constructor
     *
     * @param \XLite\Logic\Import\Importer $importer Importer
     * @param integer                      $index    Step index
     *
     * @return void
     */
    public function __construct(\XLite\Logic\Import\Importer $importer, $index)
    {
        $this->importer = $importer;
        $this->index = $index;
    }

    /**
     * Check valid state of step
     *
     * @return boolean
     */
    public function isValid()
    {
        return true;
    }

    /**
     * Check - step is current or not
     * 
     * @return boolean
     */
    public function isCurrentStep()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')
            ->getEventState(\XLite\Logic\Import\Importer::getEventName());

        return $state
            && !empty($state['options'])
            && isset($state['options']['step'])
            && $state['options']['step'] == $this->index;
    }

    /**
     * Check - step is current or not
     *
     * @return boolean
     */
    public function isFutureStep()
    {
        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')
            ->getEventState(\XLite\Logic\Import\Importer::getEventName());

        return $state
            && !empty($state['options'])
            && (!isset($state['options']['step']) || $state['options']['step'] < $this->index);
    }

    /**
     * Check - step is finalized or not
     * 
     * @return boolean
     */
    public function isStepFinalized()
    {
        \XLite\Core\TmpVars::getInstance()->lastImportStep = get_class($this);

        $state = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')
            ->getEventState(\XLite\Logic\Import\Importer::getEventName());

        return $state
            && !empty($state['options'])
            && (
                (isset($state['options']['step']) && $state['options']['step'] > $this->index)
                || isset($state['state']) && $state['state'] == \XLite\Core\EventTask::STATE_FINISHED
            );
    }

    /**
     * Check - allowed step or not
     * 
     * @return boolean
     */
    public function isAllowed()
    {
        return true;
    }

    /**
     * Check - step's work has been done or not
     * 
     * @return boolean
     */
    public function isStepDone()
    {
        return $this->isStepFinalized();
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
     * Get importer
     *
     * @return \XLite\Logic\Import\Importer
     */
    public function getImporter()
    {
        return $this->importer;
    }

    /**
     * Get options
     *
     * @return \ArrayObject
     */
    public function getOptions()
    {
        return $this->importer->getOptions();
    }

    /**
     * Get import process tick duration
     *
     * @return void
     */
    protected function getTickDuration()
    {
        $result = null;
        if ($this->getOptions()->time && 1 < $this->getOptions()->position) {
            $result = $this->getOptions()->time / $this->getOptions()->position;

        } else {
            $tick = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getVar($this->getImportTickDurationVarName());
            if ($tick) {
                $result = $tick;
            }
        }

        return $result ? (ceil($result * 1000) / 1000) : static::DEFAUL_TICK_DURATION;
    }

    /**
     * Check - import process has errors or not
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->importer->hasErrors();
    }

    /**
     * Check - import process has warnings or not
     *
     * @return boolean
     */
    public function hasWarnings()
    {
        return $this->importer->hasWarnings();
    }

    /**
     * Initialize
     *
     * @return void
     */
    public function initialize()
    {
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setVar(
            $this->getImportTickDurationVarName(),
            $this->count() ? round($this->getOptions()->time / $this->count(), 3) : 0
        );
    }

    // {{{ SeekableIterator

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
     *
     * @return void
     */
    public function seek($position)
    {
        if ($this->getOptions()->position != $position && $position <= $this->count()) {
            $this->getOptions()->position = $position;
        }
    }

    /**
     * \SeekableIterator::current
     *
     * @return \XLite\Logic\Import\Processor\AProcessor
     */
    public function current()
    {
        return $this;
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
        $this->seek($this->key() + 1);
    }

    /**
     * \SeekableIterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * \SeekableIterator::valid
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->isValid()
            && $this->key() < $this->count();
    }

    /**
     * Get importTickDuration TmpVar name
     *
     * @return string
     */
    protected function getImportTickDurationVarName()
    {
        return 'importTickDuration';
    }

    // }}}

    // {{{ Weight

    /**
     * Default step weight 
     * 
     * @var integer
     */
    protected $defaultWeight;

    /**
     * Get step weight 
     * 
     * @return integer
     */
    public function getWeight()
    {
        return $this->defaultWeight;
    }

    /**
     * Set default step weight 
     * 
     * @param integer $weight Step weight
     *  
     * @return void
     */
    public function setDefaultWeight($weight)
    {
        $this->defaultWeight = $weight;
    }

    // }}}

    // {{{ Result messages

    /**
     * Get messages 
     * 
     * @return array
     */
    public function getMessages()
    {
        return array();
    }

    // }}}


}
