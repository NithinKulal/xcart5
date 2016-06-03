<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Regular button
 */
class Divider extends \XLite\View\Button\AButton
{
    /**
     * Define the divider button (in cases of buttons list)
     *
     * @return boolean
     */
    public function isDivider()
    {
        return true;
    }
}
