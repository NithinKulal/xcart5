<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;


class ModuleState extends \XLite\View\FormField\Select\ASelect
{
    const ENABLED   = 'E';
    const DISABLED  = 'D';

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            '' => static::t('All modules'),
            static::ENABLED => static::t('Enabled only'),
            static::DISABLED => static::t('Disabled only'),
        ];
    }

}
