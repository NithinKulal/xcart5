<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Textarea;

/**
 * Order customer note
 */
class OrderCustomerNote extends \XLite\View\FormField\Inline\Textarea\OrderStaffNote
{
    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $class = parent::getContainerClass()
            . ' inline-order-customer-note';

        return trim($class);
    }
}
