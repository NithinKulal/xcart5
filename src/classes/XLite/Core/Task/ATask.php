<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Task;

/**
 * Abstract task
 */
abstract class ATask extends \XLite\Base
{
    /**
     * Model
     *
     * @var \XLite\Model\Task
     */
    protected $model;

    /**
     * Last step flag
     *
     * @var boolean
     */
    protected $lastStep = false;

    /**
     * Result operation message
     *
     * @var string
     */
    protected $message;

    /**
     * Get title
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * Run step
     */
    abstract protected function runStep();

    /**
     * Constructor
     *
     * @param \XLite\Model\Task $model Model
     */
    public function __construct(\XLite\Model\Task $model)
    {
        $this->model = $model;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Check - task ready or not
     *
     * @return boolean
     */
    public function isReady()
    {
        return true;
    }

    /**
     * Should task started if previous attempt has failed
     *
     * @return boolean
     */
    public function shouldRunIfCrashed()
    {
        return true;
    }

    /**
     * Lock key
     *
     * @return string
     */
    public function getLockKey()
    {
        return get_class() . $this->model->getId();
    }

    /**
     * Check - task ready or not
     *
     * @return boolean
     */
    public function isRunning()
    {
        return \XLite\Core\Lock\FileLock::getInstance()->isRunning(
            $this->getLockKey(),
            !$this->shouldRunIfCrashed()
        );
    }

    /**
     * Mark task as running
     *
     * @return void
     */
    protected function markAsRunning()
    {
        \XLite\Core\Lock\FileLock::getInstance()->setRunning(
            $this->getLockKey()
        );
    }

    /**
     * mark as not running
     *
     * @return void
     */
    protected function release()
    {
        \XLite\Core\Lock\FileLock::getInstance()->release(
            $this->getLockKey()
        );
    }

    /**
     * Run task
     */
    public function run()
    {
        if ($this->isValid()) {
            $this->prepareStep();

            $this->markAsRunning();

            $this->runStep();

            if ($this->isLastStep()) {
                $this->finalizeTask();

            } else {
                $this->finalizeStep();
            }
        } elseif (!$this->message) {
            $this->message = 'invalid';
        }
    }

    /**
     * Prepare step
     *
     * @return void
     */
    protected function prepareStep()
    {
    }

    /**
     * Check - current step is last or not
     *
     * @return boolean
     */
    protected function isLastStep()
    {
        return $this->lastStep;
    }

    /**
     * Finalize task (last step)
     */
    protected function finalizeTask()
    {
        $this->release();
        $this->close();
    }

    /**
     * Finalize step
     */
    protected function finalizeStep()
    {
    }

    /**
     * Check availability
     *
     * @return boolean
     */
    protected function isValid()
    {
        return true;
    }

    /**
     * Close task
     */
    protected function close()
    {
        \XLite\Core\Database::getEM()->remove($this->model);
    }
}
