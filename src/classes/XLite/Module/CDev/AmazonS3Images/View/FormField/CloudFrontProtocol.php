<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\View\FormField;

/**
 * Cloud Front protocol selector for settings page
 */
class CloudFrontProtocol extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'http_or_https' => static::t('HTTP or HTTPS'),
            'https_only' => static::t('HTTPS only'),
        );
    }
}
