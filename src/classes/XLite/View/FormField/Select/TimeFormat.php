<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Time format selector
 */
class TimeFormat extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $time = \XLite\Core\Converter::time();

        return array(
            '%T'  => \XLite\Core\Converter::formatDate($time, '%T'),
            '%H:%M' => \XLite\Core\Converter::formatDate($time, '%H:%M'),
            '%I:%M %p'  => \XLite\Core\Converter::formatDate($time, '%I:%M %p'),
            '%r'  => \XLite\Core\Converter::formatDate($time, '%r'),
        );
    }
}
