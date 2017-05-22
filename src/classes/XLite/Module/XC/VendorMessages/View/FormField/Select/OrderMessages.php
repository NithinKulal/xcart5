<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\FormField\Select;

/**
 * Order messages selector
 */
class OrderMessages extends \XLite\View\FormField\Select\Regular
{

    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        return array(
            ''  => static::t('All orders'),
            'U' => static::t('Orders with unread messages'),
            'A' => static::t('Orders with any messages'),
        );
    }
}
