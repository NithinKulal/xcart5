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
class CategoryShowTitle extends \XLite\View\FormField\Select\Regular
{

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            1 => static::t('Show'),
            0 => static::t('Hide'),
        );
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();

        if ($value === true || $value === '1') {
            $value = 1;

        } elseif ($value === false || $value === '0') {
            $value = 0;
        }

        return $value;
    }
}
