<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View;

use XLite\Module\XC\ThemeTweaker;

/**
 * Abstract widget
 */
abstract class TopCategories extends \XLite\View\TopCategories implements ThemeTweaker\View\LayoutBlockInterface, \XLite\Base\IDecorator
{
    use ThemeTweaker\View\LayoutBlockTrait;

    /**
     * Returns default display mode
     *
     * @return string
     */
    protected function getDisplayMode()
    {
        if ($this->getDisplayGroup() === static::DISPLAY_GROUP_CENTER) {
            return static::DISPLAY_MODE_TREE;
        } else {
            return parent::getDisplayMode();
        }
    }
}
