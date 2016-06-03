<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View\FormField\Select;

/**
 * Tracking type selector
 */
class TrackingType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            '1' => static::t('A single domain'),
            '2' => static::t('One domain with multiple subdomains'),
            '3' => static::t('Multiple top-level domains'),
        );
    }
}
