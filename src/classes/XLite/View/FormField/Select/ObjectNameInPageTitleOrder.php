<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;


class ObjectNameInPageTitleOrder extends \XLite\View\FormField\Select\Regular
{
    const OPTION_FIRST = 'F';
    const OPTION_LAST = 'L';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::OPTION_FIRST => static::t('Object name order first'),
            static::OPTION_LAST => static::t('Object name order last')
        ];
    }
}
