<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core;

/**
 * Cache decorator
 */
class CurrencyRate extends \XLite\Base
{
    const PROVIDER_NONE             = 'none';
    const PROVIDER_WEBSERVICE_X     = 'webx';
    const PROVIDER_GOOGLE_FINANCE   = 'google';

    /**
     * Rate provider
     *
     * @var \XLite\Module\XC\MultiCurrency\Core\RateProvider\ARateProvider
     */
    protected $rateProvider = null;

    /**
     * Get currency conversion rate
     *
     * @param string $to Destination currency code (alpha-3)
     *
     * @return float
     */
    public function getRate($to)
    {
        $currency = \XLite\Core\Database::getRepo('XLite\Model\Currency')->find(
            \XLite\Core\Config::getInstance()->General->shop_currency
        );

        $from = $currency->getCode();

        return $this->getRateProvider()->getRate($from, $to);
    }

    /**
     * Protected constructor.
     * It's not possible to instantiate a derived class (using the "new" operator)
     * until that child class is not implemented public constructor
     *
     * @return void
     */
    protected function __construct()
    {
        parent::__construct();

        if (static::PROVIDER_WEBSERVICE_X == \XLite\Core\Config::getInstance()->XC->MultiCurrency->rateProvider) {
            $this->rateProvider = new \XLite\Module\XC\MultiCurrency\Core\RateProvider\WebserviceX();
        } else {
            $this->rateProvider = new \XLite\Module\XC\MultiCurrency\Core\RateProvider\GoogleFinance();
        }
    }

    /**
     * Get rate provider
     *
     * @return \XLite\Module\XC\MultiCurrency\Core\RateProvider\ARateProvider
     */
    protected function getRateProvider()
    {
        return $this->rateProvider;
    }
}