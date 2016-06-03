<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Select category show mode
 */
class EnabledDisabled extends \XLite\View\FormField\Select\Regular
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();

        if (true === $value || '1' === $value || 'Y' === $value) {
            $value = 1;

        } elseif (false === $value || '0' === $value || 'N' === $value) {
            $value = 0;
        }

        return $value;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            1 => static::t('Enabled'),
            0 => static::t('Disabled'),
        );
    }
}
