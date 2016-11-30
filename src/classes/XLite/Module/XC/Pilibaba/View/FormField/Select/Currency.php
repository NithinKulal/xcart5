<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View\FormField\Select;

/**
 * Currency selector
 */
class Currency extends \XLite\View\FormField\Select\Regular
{
    const CACHE_KEY = 'pilibaba-currency';
    const CACHE_TTL = 3600;

    /**
     * Load currency list from Pilibaba API
     *
     * @return array
     */
    protected function loadCurrencyList()
    {
        \XLite\Module\XC\Pilibaba\Main::includeLibrary();

        \PilipayConfig::setUseHttps(false);
        \PilipayConfig::setUseProductionEnv(true);
        \PilipayLogger::instance()->setHandler(
            function($level, $msg) {
                \XLite\Module\XC\Pilibaba\Model\Payment\Processor\Pilibaba::log(
                    sprintf('%s %s: %s' . PHP_EOL, date('Y-m-d H:i:s'), $level, $msg)
                );
            }
        );

        try {
            $currencies = @\PilipayCurrency::queryAll();

            $processedCurrencies = array();

            foreach ($currencies as $currency) {
                $processedCurrencies[$currency->code] = $currency->code;
            }

        } catch (\PilipayError $e) {
            \XLite\Core\TopMessage::addError('Can\'t get allowed currencies from Pilibaba. Please, try again later');
            $processedCurrencies = [];
        }

        return $processedCurrencies;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = \XLite\Core\Database::getCacheDriver()->fetch(static::CACHE_KEY);

        if (!$list) {
            $list = $this->loadCurrencyList();
            \XLite\Core\Database::getCacheDriver()->save(static::CACHE_KEY, $list, static::CACHE_TTL);
        }

        $list = array_reverse($list, true);
        $list[''] = static::t('Select Primary currency');
        $list = array_reverse($list, true);

        return $list;
    }
}
