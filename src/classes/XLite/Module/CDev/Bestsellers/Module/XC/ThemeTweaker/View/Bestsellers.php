<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Bestsellers\Module\XC\ThemeTweaker\View;

use XLite\Module\XC\ThemeTweaker;

/**
 * @Decorator\Depend ("XC\ThemeTweaker")
 */
abstract class Bestsellers extends \XLite\Module\CDev\Bestsellers\View\Bestsellers implements ThemeTweaker\View\LayoutBlockInterface, \XLite\Base\IDecorator
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * Returns default display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        return $this->getDisplayGroup() === static::DISPLAY_GROUP_SIDEBAR
            ? static::DISPLAY_MODE_STHUMB
            : parent::getDisplayMode();
    }

    /**
     * Get current widget type parameter
     *
     * @return boolean
     */
    protected function getWidgetType()
    {
        return $this->getDisplayGroup() === static::DISPLAY_GROUP_SIDEBAR
            ? static::WIDGET_TYPE_SIDEBAR
            : static::WIDGET_TYPE_CENTER;
    }
}
