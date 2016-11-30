<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\Controller\Admin;

/**
 * Custom CSS controller
 */
class CustomCss extends \XLite\Module\XC\ThemeTweaker\Controller\Admin\Base\ThemeTweaker
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->isAJAX() ? '' : static::t('Custom CSS');
    }

    public function printAJAXAttributes()
    {
        return 'data-dialog-modal="false"';
    }
}
