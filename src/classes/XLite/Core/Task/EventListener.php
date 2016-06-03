<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Task;

/**
 * Event listener (DB-based)
 */
class EventListener extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Event listener (DB)';
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        $listener = \XLite\Core\EventListener::getInstance();
        foreach (\XLite\Core\Database::getRepo('XLite\Model\EventTask')->findQuery() as $task) {
            if ($listener->handle($task->getName(), $task->getArguments())) {
                \XLite\Core\Database::getEM()->remove($task);
            }
        }
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return 1800;
    }

}
