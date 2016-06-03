<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\View\FormField;

/**
 * Freight shipping calculation mode selector
 */
class FreightMode extends \XLite\View\FormField\Select\Regular
{
    /**
     * Values
     */
    const FREIGHT_ONLY = 'F'; // Use freight fixed fee only
    const FREIGHT_ADD  = 'B'; // Add freight fixed fee to a base shipping rate

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::FREIGHT_ONLY => static::t('Shipping freight only'),
            static::FREIGHT_ADD  => static::t('Shipping freight + regular shipping rate'),
        );
    }
}
