<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\BulkEditing\Core\EventListener;

/**
 * Bulk edit
 */
class BulkEdit extends \XLite\Core\EventListener\Base\Countable
{
    const CHUNK_LENGTH = 25;

    /**
     * @var \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Generator
     */
    protected $generator;

    /**
     * @var integer
     */
    protected $counter;

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
        return 'bulkEdit';
    }

    /**
     * Process item
     *
     * @param \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Step\AStep $item Item
     *
     * @return boolean
     */
    protected function processItem($item)
    {
        $this->serviceTime += (microtime(true) - $this->timeMark);

        $result = $item->run();

        $this->timeMark = microtime(true);

        if (!$this->getItems()->valid()) {
            $result = false;
            foreach ($this->getItems()->getErrors() as $error) {
                $this->errors[] = $error['title'];
            }
        }

        return $result;
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
     * @return \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Generator
     */
    protected function getItems()
    {
        if (null === $this->generator) {
            $this->generator = new \XLite\Module\XC\BulkEditing\Logic\BulkEdit\Generator(
                isset($this->record['options']) ? $this->record['options'] : []
            );
        }

        return $this->generator;
    }

    /**
     * Initialize task
     */
    protected function initializeTask()
    {
        $this->getItems()->initialize();
    }

    /**
     * Initialize step
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
     */
    protected function finishStep()
    {
        $generator = $this->getItems();

        $this->serviceTime             += (microtime(true) - $this->timeMark);
        $generator->getOptions()->time += $this->serviceTime;

        $this->record['options'] = $generator->getOptions()->getArrayCopy();

        parent::finishStep();
    }

    /**
     * Finish task
     */
    protected function finishTask()
    {
        $this->record['options'] = $this->getItems()->getOptions()->getArrayCopy();

        parent::finishTask();

        $this->generator->finalize();
    }

    /**
     * Writes some data into $this->record['touchData'] after step/task finish.
     */
    protected function compileTouchData()
    {
        $this->record['touchData'] = [];

        $timeLabel = \XLite\Core\Translation::formatTimePeriod($this->getItems()->getTimeRemain());
        if ($timeLabel) {
            $this->record['touchData']['message'] = static::t('About X remaining', ['time' => $timeLabel]);
        }
    }

    /**
     * Check - step is success or not
     *
     * @return boolean
     */
    protected function isStepSuccess()
    {
        return parent::isStepSuccess() && !$this->getItems()->hasErrors();
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
     */
    protected function failTask()
    {
        parent::failTask();

        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->removeEventState($this->getEventName());
    }
}
