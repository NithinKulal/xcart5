<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\Add2CartPopup\View\Product;

/**
 * Product list item widget
 */
class CartPopupListItem extends \XLite\View\Product\ListItem
{
    /**
     * Disable quick-look feature
     *
     * @return boolean
     */
    protected function isQuickLookEnabled()
    {
        return false;
    }
}