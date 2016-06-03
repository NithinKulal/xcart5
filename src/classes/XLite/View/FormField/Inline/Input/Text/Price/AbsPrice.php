<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Price;

/**
 * Price widget (always displays positive value) for AOM
 */
class AbsPrice extends \XLite\View\FormField\Inline\Input\Text\Price
{
    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        return abs(parent::getFieldEntityValue($field));
    }

}
