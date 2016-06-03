<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Integer range
 */
class IntegerRange extends \XLite\View\FormField\Input\Text\ARange
{
    /**
     * Returns input widget class name
     *
     * @return string
     */
    protected function getInputWidgetClass()
    {
        return 'XLite\View\FormField\Input\Text\Integer';
    }

    /**
     * Returns end widget class
     *
     * @return string
     */
    protected function getEndWidgetClass()
    {
        return 'XLite\View\FormField\Input\Text\IntegerWithInfinity';
    }

    /**
     * Returns default begin value
     *
     * @return mixed
     */
    protected function getDefaultBeginValue()
    {
        return 0;
    }
}
