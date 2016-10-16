<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Controller\Admin;


class CacheManagement extends \XLite\Controller\Admin\CacheManagement implements \XLite\Base\IDecorator
{
    /**
     * Export action
     *
     * @return void
     */
    protected function doActionQuickData()
    {
        parent::doActionQuickData();

        \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')->recalculateTreeStructure();
    }
}