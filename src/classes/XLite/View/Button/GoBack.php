<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * "Go back" button
 */
class GoBack extends \XLite\View\Button\Regular
{
    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Go back';
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return 'self.history.go(-1)';
    }
}
