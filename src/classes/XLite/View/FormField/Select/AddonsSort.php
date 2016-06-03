<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Addons sort control
 */
class AddonsSort extends \XLite\View\FormField\Select\Base\Rich
{
    /**
     * Sort option name definitions
     */
    const SORT_OPT_POPULAR    = 'm.downloads';
    const SORT_OPT_NEWEST     = 'm.revisionDate';
    const SORT_OPT_ALPHA      = 'm.moduleName';


    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            static::SORT_OPT_POPULAR => static::t('Most Popular'),
            static::SORT_OPT_NEWEST  => static::t('Newest'),
            static::SORT_OPT_ALPHA   => static::t('Alphabetically'),
        );
    }
}
