<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\EventListener;

/**
 * Import
 */
class Import extends \XLite\Core\EventListener\Base\Countable
{
    const CHUNK_LENGTH = 30;

    /**
     * Importer
     *
     * @var \XLite\Logic\Import\Importer
     */
    protected $importer;

    /**
     * Time mark
     *
     * @var integer
     */
    protected $timeMark = 0;

    /**
     * Service time
     *
     * @var integer
     */
    protected $serviceTime = 0;

    /**
     * Get event name
     *
     * @return string
     */
    protected function getEventName()
    {
        return 'import';
    }

    /**
     * Process item
     *
     * @param mixed $item Item
     *
     * @return boolean
     */
    protected function processItem($item)
    {
        $this->serviceTime += (microtime(true) - $this->timeMark);

        if (0 === $item->current()->key()) {
            $item->current()->seek($this->record['position']);
        }

        if (0 === $item->current()->key()) {
            $item->current()->initialize();
        }

        $item->current()->process();
        $this->record['position'] = $item->current()->key();
        $this->record['options'] = $item->current()->getOptions()->getArrayCopy();

        $this->timeMark = microtime(true);

        return true;
    }

    /**
     * Check step valid state
     *
     * @return boolean
     */
    protected function isStepValid()
    {
        return parent::isStepValid()
            && $this->getItems()->valid();
    }

    /**
     * Get images list length
     *
     * @return integer
     */
    protected function getLength()
    {
        return $this->getItems()->count();
    }

    /**
     * Get items
     *
     * @return array
     */
    protected function getItems()
    {
        if (!isset($this->importer)) {
            $this->importer = new \XLite\Logic\Import\Importer(isset($this->record['options']) ? $this->record['options'] : array());
        }

        return $this->importer->getStep();
    }

    /**
     * Initialize step
     *
     * @return void
     */
    protected function initializeStep()
    {
        $this->timeMark = microtime(true);

        set_time_limit(0);
        $this->counter = static::CHUNK_LENGTH;

        parent::initializeStep();
    }

    /**
     * Finish step
     *
     * @return void
     */
    protected function finishStep()
    {
        $step = $this->getItems();

        $this->serviceTime += (microtime(true) - $this->timeMark);
        $step->getOptions()->time += $this->serviceTime;

        $this->record['options'] = $step->getOptions()->getArrayCopy();

        parent::finishStep();
    }

    /**
     * Finish task
     *
     * @return void
     */
    protected function finishTask()
    {
        $this->getItems()->finalize();

        parent::finishTask();

        if (!$this->getItems()->getImporter()->isNextStepAllowed()) {
            $this->getItems()->getImporter()->finalize();
            $this->record['options'] = $this->getItems()->getOptions()->getArrayCopy();

            if (\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName())) {
                \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setEventState($this->getEventName(), $this->record);
            }
        }
    }

    /**
     * Writes some data into $this->record['touchData'] after step/task finish.
     */
    protected function compileTouchData()
    {
        $this->record['touchData'] = array();

        if (0 < ($this->getItems()->getOptions()->errorsCount + $this->getItems()->getOptions()->warningsCount)) {
            $label = $this->getItems()->getErrorLanguageLabel();

        } else {
            $label = $this->getItems()->getNormalLanguageLabel();
        }

        $this->record['touchData']['message'] = $label;
    }

    /**
     * Check - step is success or not
     *
     * @return boolean
     */
    protected function isStepSuccess()
    {
        return parent::isStepSuccess()
            && (
               0 == $this->getItems()->getOptions()->step
               || !$this->getItems()->hasErrors()
            );
    }

    /**
     * Check - continue cycle or not
     *
     * @param mixed $item Item
     *
     * @return boolean
     */
    protected function isContinue($item)
    {
        $this->counter--;

        return parent::isContinue($item) && 0 < $this->counter && empty($this->errors);
    }

    /**
     * Fail task
     *
     * @return void
     */
    protected function failTask()
    {
        parent::failTask();

        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState($this->getEventName());
    }

}

