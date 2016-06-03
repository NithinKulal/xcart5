<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\VolumeDiscounts\View\FormField;

/**
 * Discount type form field
 */
class DiscountType extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/VolumeDiscounts/form_field/discount_type.js';

        return $list;
    }

    /**
     * getContainerClass 
     * 
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-discountType';
    }

    /**
     * defineFieldClass 
     * 
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\Module\CDev\VolumeDiscounts\View\FormField\SelectDiscountType';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'modules/CDev/VolumeDiscounts/form_field/discount_type_view.twig';
    }
}
