<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Task\Base;

/**
 * Abstract periodic task
 */
abstract class Periodic extends \XLite\Core\Task\ATask
{
    /**
     * Get period (seconds)
     *
     * @return integer
     */
    abstract protected function getPeriod();

    /**
     * Mark task as running
     * 
     * @return void
     */
    protected function markAsRunning()
    {
        \XLite\Core\Lock\FileLock::getInstance()->setRunning(
            $this->getLockKey(),
            $this->getPeriod() / 4
        );
    }

    /**
     * Finalize step
     *
     * @return void
     */
    protected function finalizeStep()
    {
        parent::finalizeStep();

        $this->release();

        $this->model->setTriggerTime(\XLite\Core\Converter::time() + $this->getPeriod());
    }

}
