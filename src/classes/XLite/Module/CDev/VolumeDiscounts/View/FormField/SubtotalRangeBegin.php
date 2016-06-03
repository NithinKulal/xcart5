<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\FormField;

/**
 * Subtotal range begin form field
 */
class SubtotalRangeBegin extends \XLite\View\FormField\Inline\Input\Text\Price
{
    /**
     * getContainerClass 
     * 
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-subtotalRangeBegin';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'modules/CDev/VolumeDiscounts/form_field/subtotal_view.twig';
    }
}
