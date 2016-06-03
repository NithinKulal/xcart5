<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Checkbox\Switcher;

/**
 * Enabled state switcher
 */
class Enabled extends \XLite\View\FormField\Inline\Input\Checkbox\Switcher
{
    /**
     * Preprocess value forsave
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    protected function preprocessSavedValue($value)
    {
        return (bool)$value;
    }

    /**
     * Check - field has view or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }

}

