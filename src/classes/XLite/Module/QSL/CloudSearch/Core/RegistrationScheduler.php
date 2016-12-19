<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\QSL\CloudSearch\Core;

use XLite\Core\Database;

/**
 * CloudSearch registration process scheduler
 */
class RegistrationScheduler extends \XLite\Base\Singleton
{
    /**
     * Scheduled registration flag name
     */
    const REGISTRATION_SCHEDULED = 'cloud_search_reg_scheduled';

    /**
     * Schedule registration at the first opportunity, but not now
     *
     * @return void
     */
    public function schedule()
    {
        Database::getRepo('XLite\Model\TmpVar')->setVar(static::REGISTRATION_SCHEDULED, true);
    }

    /**
     * Remove registration from schedule
     *
     * @return void
     */
    public function unschedule()
    {
        Database::getRepo('XLite\Model\TmpVar')->setVar(static::REGISTRATION_SCHEDULED, false);
    }

    /**
     * Register CloudSearch if it has been scheduled
     *
     * @return void
     */
    public function registerIfScheduled()
    {
        if ($this->isScheduled() && !\XLite::isCacheBuilding()) {
            $apiClient = new ServiceApiClient();

            $this->unschedule();

            $apiClient->register();
        }
    }

    /**
     * Check if registration was scheduled
     *
     * @return bool
     */
    public function isScheduled()
    {
        return (bool)Database::getRepo('XLite\Model\TmpVar')->getVar(static::REGISTRATION_SCHEDULED);
    }
}