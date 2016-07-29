<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\FormField\Select;

/**
 * API version selector
 */
class APIVersion extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            '1.1' => static::t('1.1 (X-Payments 1.0.5 and earlier)'),
            '1.2' => static::t('1.2 (X-Payments 2.0 and X-Payments 1.0.6)'),
            '1.3' => static::t('1.3 (X-Payments 2.1.1)'),
            '1.4' => static::t('1.3 (X-Payments 2.1.2)'),
            '1.5' => static::t('1.3 (X-Payments 2.2)'),
        );
    }
}
