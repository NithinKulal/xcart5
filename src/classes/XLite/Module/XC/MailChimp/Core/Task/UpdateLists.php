<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Task;

use \XLite\Module\XC\MailChimp\Core\MailChimp;

/**
 * Scheduled task that sends automatic cart reminders.
 */
class UpdateLists extends \XLite\Core\Task\Base\Periodic
{
    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('MailChimp lists update');
    }

    /**
     * Run step
     *
     * @return void
     */
    protected function runStep()
    {
        MailChimp::getInstance()->updateMailChimpLists();
    }

    /**
     * Get period (seconds)
     *
     * @return integer
     */
    protected function getPeriod()
    {
        return \XLite\Core\Config::getInstance()->XC->MailChimp->updateInterval;
    }

    /**
     * Task is valid only if API key has been  specified
     *
     * @return boolean
     */
    protected function isValid()
    {
        return MailChimp::hasAPIKey();
    }
}
