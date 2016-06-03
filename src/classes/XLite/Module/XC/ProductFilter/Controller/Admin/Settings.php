<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductFilter\Controller\Admin;

/**
 * Settings page controller
 */
class Settings extends \XLite\Controller\Admin\Settings implements \XLite\Base\IDecorator
{
    /**
     * Remove product filter cache
     *
     * @return void
     */
    public function doActionRemoveProductFilterCache()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Category')->removeProductFilterCache();

        \XLite\Core\TopMessage::addInfo('The product filter cache has been removed successfully.');
    }
}