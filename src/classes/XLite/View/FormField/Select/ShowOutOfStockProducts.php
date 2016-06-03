<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Select "How to show out of stock products"
 */
class ShowOutOfStockProducts extends \XLite\View\FormField\Select\Regular
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'everywhere'   => static::t('Show in all the sections'),
            'catAndSearch' => static::t('Show only in categories and search listings'),
            'directLink'   => static::t('Hide and make them available only via a direct link'),
        );
    }
}
