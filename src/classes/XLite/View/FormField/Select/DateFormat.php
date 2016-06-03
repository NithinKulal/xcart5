<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Date format selector
 */
class DateFormat extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $time = \XLite\Core\Converter::time();

        $allowedDateFormats = \XLite\Core\Converter::getAvailableDateFormats();
        
        $options = array();
        foreach ($allowedDateFormats as $phpFormat => $formats) {
            $options[$phpFormat] = \XLite\Core\Converter::formatDate($time, $phpFormat);
        }

        return $options;
    }
}
