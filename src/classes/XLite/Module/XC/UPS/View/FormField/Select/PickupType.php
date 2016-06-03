<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\View\FormField\Select;

/**
 * Pickup type selector for settings page
 */
class PickupType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            '01' => static::t('Daily Pickup'),
            '03' => static::t('Customer counter'),
            '06' => static::t('One time pickup'),
            '07' => static::t('On call air'), // Will be ignored when negotiated rates are requested
            '19' => static::t('Letter center'),
            '20' => static::t('Air service center'),
        );
    }
}
