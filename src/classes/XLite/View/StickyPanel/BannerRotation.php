<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel;

/**
 * Banner rotation sticky panel
 */
class BannerRotation extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        $class = parent::getClass();

        $class = trim($class . ' images-settings-panel');

        return $class;
    }
}
