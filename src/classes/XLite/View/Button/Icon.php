<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Icon-based button
 */
class Icon extends \XLite\View\Button\Regular
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/icon.twig';
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function  getClass()
    {
        return trim(parent::getClass() . ' left-icon-based');
    }

    /**
     * Icon place into left border or not
     * 
     * @return boolean
     */
    protected function isLeftIcon()
    {
        return true;
    }
}
