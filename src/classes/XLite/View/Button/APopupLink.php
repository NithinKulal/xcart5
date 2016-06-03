<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Button to use with popup (with defined AUTOLOADing of JS object)
 */
abstract class APopupLink extends \XLite\View\Button\APopupButton
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/popup_link.twig';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return '';
    }
}
