<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\EventListener\Base;

/**
 * Abstract countable task
 */
abstract class Countable extends \XLite\Core\EventListener\AEventListener
{
    /**
     * Event record 
     * 
     * @var array
     */
    protected $record;

    /**
     * Get event name 
     * 
     * @return string
     */
    abstract protected function getEventName();

    /**
     * Get length 
     * 
     * @return integer
     */
    abstract protected function getLength();

    /**
     * Get items 
     * 
     * @return array
     */
    abstract protected function getItems();

    /**
     * Process item 
     * 
     * @param mixed $item Item
     *  
     * @return boolean
     */
    abstract protected function processItem($item);

    /**
     * Handle event (internal, after checking)
     *
     * @param string $name      Event name
     * @param array  $arguments Event arguments OPTIONAL
     *
     * @return boolean
     */
    public function handleEvent($name, array $arguments)
    {
        parent::handleEvent($name, $arguments);

        $this->errors = array();

        $result = false;

        $this->initializeStep();

        if (0 == $this->record['position']) {
            $this->initializeTask();
        }

        if ($this->isStepValid()) {

            $this->startStep();
            $this->runCurrentStep();

            if ($this->record['length'] <= $this->record['position']) {
                $this->finishTask();

            } else {
                $this->finishStep();
            }

            $result = true;

        } else {
            $this->failTask();
        }

        return $result;
    }

    /**
     * Initialize step 
     * 
     * @return void
     */
    protected function initializeStep()
    {
        $this->record = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName());
        $this->record['state'] = \XLite\Core\EventTask::STATE_IN_PROGRESS;
        \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setEventState($this->getEventName(), $this->record);
    }

    /**
     * Initialize task 
     * 
     * @return void
     */
    protected function initializeTask()
    {
    }

    /**
     * Check step valid state
     * 
     * @return boolean
     */
    protected function isStepValid()
    {
        return !empty($this->record);
    }

    /**
     * Start step 
     * 
     * @return void
     */
    protected function startStep()
    {
        if (0 == $this->record['length']) {
            $this->record['length'] = $this->getLength();
        }
    }

    /**
     * Run current step 
     * 
     * @return void
     */
    protected function runCurrentStep()
    {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\TmpVar');
        $items = $this->getItems();
        if ($items) {
            foreach ($this->getItems() as $item) {
                if ($this->processItem($item)) {
                    $this->record['position']++;
                    if ($repo->getEventState($this->getEventName())) {
                        $repo->setEventState($this->getEventName(), $this->record, false);
                    }
                }
                if (!$this->isContinue($item)) {
                    break;
                }
            }

            \XLite\Core\Database::getEM()->flush();
        } else {
            // No items found - finish process
            $this->record['position'] = $this->record['length'] + 1;
        }
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
        return true;
    }

    /**
     * Finish step 
     * 
     * @return void
     */
    protected function finishStep()
    {
        $this->compileTouchData();
        $this->record['state'] = $this->isStepSuccess() ? \XLite\Core\EventTask::STATE_STANDBY : \XLite\Core\EventTask::STATE_ABORTED;
        if (\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName())) {
            \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setEventState($this->getEventName(), $this->record);

            $event = $this->getEventName();
            // Method name assembled into getEventName() method
            \XLite\Core\EventTask::$event($this->arguments);
        }
    }

    /**
     * Finish task 
     * 
     * @return void
     */
    protected function finishTask()
    {
        $this->compileTouchData();
        $this->record['state'] = $this->isStepSuccess() ? \XLite\Core\EventTask::STATE_FINISHED : \XLite\Core\EventTask::STATE_ABORTED;
        if (\XLite\Core\Database::getRepo('XLite\Model\TmpVar')->getEventState($this->getEventName())) {
            \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->setEventState($this->getEventName(), $this->record);
        }
    }

    /**
     * Writes some data into $this->record['touchData'] after step/task finish.
     */
    protected function compileTouchData()
    {
    }

    /**
     * Check - step is success or not
     * 
     * @return boolean
     */
    protected function isStepSuccess()
    {
        return true;
    }

    /**
     * Fail task
     *
     * @return void
     */
    protected function failTask()
    {
    }    
}

