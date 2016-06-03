<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Checkout buttons separator
 */
class ButtonsSeparator extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/buttons_separator.twig';
    }

    /**
     * Check if the "cart totals" block is the current one
     * 
     * @return boolean
     */
    protected function isCartTotalsBlock()
    {
        return !empty($this->viewListName) && 'cart.panel.totals' == $this->viewListName;
    }
}
