<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Form;

/**
 * Code form
 */
class Code extends \XLite\View\Form\AForm
{
    /**
     * Get default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return \XLite\Core\Request::getInstance()->target;
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'save';
    }

}
