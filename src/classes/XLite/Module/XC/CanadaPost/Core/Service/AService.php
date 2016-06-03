<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Core\Service;

/**
 * Abstract Canada Post service class
 */
abstract class AService extends \XLite\Module\XC\CanadaPost\Core\API
{
    // {{{ Common methods

    /**
     * Adjust float $value with $precision and within limits ($min and $max)
     *
     * @param float $value     Amount
     * @param int   $precision Precision
     * @param float $min       Min amount
     * @param float $max       Max amount
     *
     * @return float
     */
    public static function adjustFloatValue($value, $precision, $min, $max)
    {
        return min($max, max($min, \XLite\Logic\Math::getInstance()->round($value, $precision)));
    }

    // }}}
}
