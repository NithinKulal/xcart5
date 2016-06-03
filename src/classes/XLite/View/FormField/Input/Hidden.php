<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Hidden
 */
class Hidden extends \XLite\View\FormField\Input\Base\StringInput
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_HIDDEN;
    }

}
