<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\FormField\Select;

use XLite\Module\XC\MailChimp\Core\Task\UpdateLists;

/**
 * Update interval selector
 */
class UpdateInterval extends \XLite\View\FormField\Select\Regular
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return \XLite\Core\Config::getInstance()->XC->MailChimp->updateInterval;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            0                           => static::t('Never'),
            UpdateLists::INT_1_MIN      => static::t('1 minute'),
            UpdateLists::INT_10_MIN     => static::t('10 minutes'),
            UpdateLists::INT_15_MIN     => static::t('15 minutes'),
            UpdateLists::INT_30_MIN     => static::t('30 minutes'),
            UpdateLists::INT_1_HOUR     => static::t('1 hour'),
            UpdateLists::INT_2_HOURS    => static::t('2 hours'),
            UpdateLists::INT_4_HOURS    => static::t('4 hours'),
            UpdateLists::INT_6_HOURS    => static::t('6 hours'),
            UpdateLists::INT_12_HOURS   => static::t('12 hours'),
            UpdateLists::INT_1_DAY      => static::t('1 day'),
            UpdateLists::INT_2_DAYS     => static::t('2 days'),
            UpdateLists::INT_5_DAYS     => static::t('5 days'),
            UpdateLists::INT_1_WEEK     => static::t('1 week'),
        );
    }
}