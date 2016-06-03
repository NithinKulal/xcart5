<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Product price
 */
class Surcharge extends \XLite\View\Surcharge implements \XLite\Base\IDecorator
{
    const PARAM_NO_CONVERSION = 'noConversion';

    /**
     * Format price as HTML block
     *
     * @param float                 $value             Value
     * @param \XLite\Model\Currency $currency          Currency OPTIONAL
     * @param boolean               $strictFormat      Flag if the price format is strict (trailing zeroes and so on options) OPTIONAL
     * @param boolean               $noValueConversion Do not use value conversion OPTIONAL
     *
     * @return string
     */
    public function formatPriceHTML($value, \XLite\Model\Currency $currency = null, $strictFormat = false, $noValueConversion = false)
    {
        $noConversion = $this->getParam(self::PARAM_NO_CONVERSION) || $noValueConversion ? true : false;

        return parent::formatPriceHTML($value, $currency, $strictFormat, $noConversion);
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_NO_CONVERSION => new \XLite\Model\WidgetParam\TypeBool('No conversion', null)
        );
    }
}