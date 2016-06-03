<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ColorSchemes\View\LayoutSettings;

/**
 * Layout settings
 */
class Settings extends \XLite\View\LayoutSettings\Settings implements \XLite\Base\IDecorator
{
    /**
     * Returns current skin name
     *
     * @return string
     */
    protected function getCurrentSkinName()
    {
        return \XLite\Core\Layout::getInstance()->getLayoutColor()
            ? parent::getCurrentSkinName()
            : static::t('Standard');
    }
}
