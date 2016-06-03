<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\FormField;

/**
 * Discount value form field
 */
class DiscountValue extends \XLite\View\FormField\Inline\Input\Text\FloatInput
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/VolumeDiscounts/form_field/discount_value.js';

        return $list;
    }

    /**
     * getContainerClass 
     * 
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-discountValue';
    }

    /**
     * Return field template
     *
     * @return string
     */

    protected function getViewTemplate()
    {
        return 'modules/CDev/VolumeDiscounts/form_field/discount_value_view.twig';
    }

    /**
     * Get precision for discount value (4 digits after point)
     *
     * @return integer
     */
    protected function getE()
    {
        return 4;
    }
}
