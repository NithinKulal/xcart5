<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\RadioButtonsList;

/**
 * Yes / No radio buttons list
 */
class YesNo extends ARadioButtonsList
{
    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'Y'   => static::t('Yes'),
            'N'   => static::t('No'),
        );
    }
}
