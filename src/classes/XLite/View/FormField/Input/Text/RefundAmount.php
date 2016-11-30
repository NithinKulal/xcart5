<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;


class RefundAmount extends \XLite\View\FormField\Input\Text\Price
{
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'refund-amount';
        $classes[] = 'not-affect-recalculate';
        $classes[] = 'not-significant';
        $classes[] = 'not-save';

        return $classes;
    }
}