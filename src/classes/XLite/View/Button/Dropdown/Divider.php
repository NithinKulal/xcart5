<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Dropdown;

/**
 * Regular button
 */
class Divider extends \XLite\View\Button\AButton
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/divider.twig';
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return 'divider '
        . $this->getParam(static::PARAM_STYLE);
    }
}
