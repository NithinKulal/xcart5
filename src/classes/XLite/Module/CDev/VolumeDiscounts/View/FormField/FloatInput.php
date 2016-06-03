<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\FormField;

/**
 * Extended Float form field
 */
abstract class FloatInput extends \XLite\View\FormField\Input\Text\FloatInput implements \XLite\Base\IDecorator
{
    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_MIN]->setValue(0);
    }

    /**
     * Format value
     *
     * @param float $value Float value
     *
     * @return string
     */
    protected function formatValue($value)
    {
        $str = sprintf('%0.f', $value);
        $precision = strlen(sprintf('%d', intval(substr($str, strpos($str, '.') + 1))));
        $result = round($value, $precision);

        return $result;
    }
}
