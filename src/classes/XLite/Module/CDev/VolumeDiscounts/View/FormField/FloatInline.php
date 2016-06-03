<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\FormField;

/**
 * Extended Float form field
 */
abstract class FloatInline extends \XLite\View\FormField\Inline\Input\Text\FloatInput implements \XLite\Base\IDecorator
{
    /**
     * Get formatted value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->formatValue(parent::getValue());
    }
}
