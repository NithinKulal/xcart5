<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\RadioButtonsList;

/**
 * CSV delimiter
 */
class CSVDelimiter extends \XLite\View\FormField\Select\RadioButtonsList\ARadioButtonsList
{
    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            ','   => static::t('Comma'),
            ';'   => static::t('Semicolon'),
        );
    }
}
