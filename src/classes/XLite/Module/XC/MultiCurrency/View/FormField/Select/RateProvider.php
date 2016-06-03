<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\FormField\Select;

/**
 * Rate provider select class
 */
class RateProvider extends \XLite\View\FormField\Select\Regular
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return \XLite\Core\Config::getInstance()->XC->MultiCurrency->rateProvider;
    }

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            \XLite\Module\XC\MultiCurrency\Core\CurrencyRate::PROVIDER_NONE             => static::t(
                'None'
            ),
            \XLite\Module\XC\MultiCurrency\Core\CurrencyRate::PROVIDER_GOOGLE_FINANCE   => static::t(
                'Google Finance Currency Converter'
            ),
            \XLite\Module\XC\MultiCurrency\Core\CurrencyRate::PROVIDER_WEBSERVICE_X     => static::t(
                'WebserviceX.NET'
            )
        );
    }
}