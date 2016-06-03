<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\CheckboxList;

/**
 * Multiple select "Yes / No"
 */
class YesNo extends \XLite\View\FormField\Select\CheckboxList\Simple
{
    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            1 => static::t('Yes'),
            0 => static::t('No'),
        );
    }
}
