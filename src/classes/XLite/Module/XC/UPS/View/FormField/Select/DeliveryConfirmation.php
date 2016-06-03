<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\View\FormField\Select;

/**
 * Delivery confirmation selector for settings page
 */
class DeliveryConfirmation extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            '0' => static::t('No confirmation'),
            '1' => static::t('Delivery confirmation - no signature'),
            '2' => static::t('Delivery confirmation - signature required'),
            '3' => static::t('Delivery confirmation - adult signature required'),
        );
    }
}
