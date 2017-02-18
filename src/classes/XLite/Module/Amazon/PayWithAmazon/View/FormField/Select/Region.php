<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View\FormField\Select;

/**
 * Region selector
 */
class Region extends \XLite\View\FormField\Select\Regular
{
    /**
     * @var array
     */
    protected static $regions = [
        'us' => 'USD',
        'uk' => 'GBP',
        'de' => 'EUR',
        'jp' => 'JPY',
    ];

    /**
     * @param $region
     *
     * @return string
     */
    public static function getCurrencyByRegion($region)
    {
        return isset(static::$regions[$region]) ? static::$regions[$region] : '';
    }

    /**
     * @param $currency
     *
     * @return string
     */
    public static function getRegionByCurrency($currency)
    {
        $currencies = array_flip(static::$regions);

        return isset($currencies[$currency]) ? $currencies[$currency] : '';
    }

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'USD' => 'United States (USD)',
            'GBP' => 'United Kingdom (GBP)',
            'EUR' => 'Germany (EUR)',
            // 'JPY' => 'Japan (JPY)',
        );
    }
}
