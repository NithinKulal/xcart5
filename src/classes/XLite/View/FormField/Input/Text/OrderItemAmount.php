<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Order item amount
 */
class OrderItemAmount extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Check maximum value: always return true as order item validation
     * performed in items list
     *
     * @return boolean
     */
    protected function checkMaxValue()
    {
        return true;
    }
}
