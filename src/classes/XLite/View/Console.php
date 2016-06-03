<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Console mode main widget
 */
class Console extends \XLite\View\AView
{
    /**
     * Attempts to display widget using its template
     *
     * @param string $template Template file name OPTIONAL
     *
     * @return void
     */
    public function display($template = null)
    {
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }
}
