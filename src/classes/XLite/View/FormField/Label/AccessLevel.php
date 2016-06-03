<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Label;

/**
 * Measure
 */
class AccessLevel extends \XLite\View\FormField\Label
{
    /**
     * getValue
     *
     * @return string
     */
    public function getValue()
    {
        $userTypes = \XLite\Core\Auth::getInstance()->getUserTypesRaw();
        $value = parent::getValue();
        
        return isset($userTypes[$value]) ? $userTypes[$value] : 'undefined';
    }
}
