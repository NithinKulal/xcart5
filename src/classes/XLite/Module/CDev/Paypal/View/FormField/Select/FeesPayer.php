<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Select;

/**
 * FeesPayer selector (used in Paypal Adaptive Payments only for now)
 */
class FeesPayer extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'SENDER'            => static::t('SENDER'),
            'PRIMARYRECEIVER'   => static::t('PRIMARYRECEIVER'),
            'EACHRECEIVER'      => static::t('EACHRECEIVER'),
            'SECONDARYONLY'     => static::t('SECONDARYONLY'),
        );
    }
}
