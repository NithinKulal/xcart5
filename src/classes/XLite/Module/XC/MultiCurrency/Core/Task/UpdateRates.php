<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core\Task;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Scheduled task that sends automatic cart reminders.
 */
class UpdateRates extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Rates update');
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        MultiCurrency::getInstance()->updateRates();
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return \XLite\Core\Config::getInstance()->XC->MultiCurrency->updateInterval;
    }
}