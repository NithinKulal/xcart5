<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Core\RateProvider;

/**
 * Cache decorator
 */
abstract class ARateProvider
{
    /**
     * Get currency conversion rate
     *
     * @param string $from Source currency code (alpha-3)
     * @param string $to   Destination currency code (alpha-3)
     *
     * @return float
     */
    abstract public function getRate($from, $to);
}