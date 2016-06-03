<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * On/Off FlipSwitch
 */
class OnOffWithoutOffLabel extends \XLite\View\FormField\Input\Checkbox\OnOff
{
    /**
     * Returns param value
     *
     * @return string
     */
    protected function getOffLabel()
    {
        // onoff switcher was restyled, so this is unused
        return parent::getOffLabel();
    }
}
