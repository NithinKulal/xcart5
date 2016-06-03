<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select\Product;

/**
 * "Use separate box" complex form field
 */
class UseSeparateBox extends \XLite\View\FormField\Select\YesNo
{
    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'product/use_separate_box.twig';
    }

    /**
     * Return field template for parent selector
     *
     * @return string
     */
    protected function getSelectorFieldTemplate()
    {
        return parent::getFieldTemplate();
    }
}
