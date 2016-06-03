<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Select;

use \XLite\Module\XC\MultiCurrency\Core\Task\UpdateRates;

/**
 * Rate provider select class
 */
class RateUpdateInterval extends \XLite\View\FormField\Select\Regular
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return \XLite\Core\Config::getInstance()->XC->MultiCurrency->updateInterval;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            UpdateRates::INT_1_MIN      => static::t('1 minute'),
            UpdateRates::INT_10_MIN     => static::t('10 minutes'),
            UpdateRates::INT_15_MIN     => static::t('15 minutes'),
            UpdateRates::INT_30_MIN     => static::t('30 minutes'),
            UpdateRates::INT_1_HOUR     => static::t('1 hour'),
            UpdateRates::INT_2_HOURS    => static::t('2 hours'),
            UpdateRates::INT_4_HOURS    => static::t('4 hours'),
            UpdateRates::INT_6_HOURS    => static::t('6 hours'),
            UpdateRates::INT_12_HOURS   => static::t('12 hours'),
            UpdateRates::INT_1_DAY      => static::t('1 day'),
        );
    }
}