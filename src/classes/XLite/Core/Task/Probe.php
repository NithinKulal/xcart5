<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Task;

/**
 * Probe task
 */
class Probe extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Benchmark';
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        \XLite\Core\Probe::getInstance()->measure();
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return 3600;
    }

}
