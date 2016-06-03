<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\FormField\Select;

/**
 * Banner position selector
 */
class BannerOnHomePage extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'D' => static::t('Disabled'),
            'A' => static::t('Show above products'),
            'B' => static::t('Show below products'),
        );
    }
}
