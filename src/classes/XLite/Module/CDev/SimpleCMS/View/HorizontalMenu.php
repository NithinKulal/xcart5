<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View;

/**
 * Main menu
 *
 * @Decorator\Depend("QSL\HorizontalCategoriesMenu")
 */
class HorizontalMenu extends \XLite\Module\QSL\HorizontalCategoriesMenu\View\HorizontalMenu implements \XLite\Base\IDecorator
{
   /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return false;
    } 
}

