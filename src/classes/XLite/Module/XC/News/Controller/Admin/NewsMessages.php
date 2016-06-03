<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\Controller\Admin;

/**
 * News messages controller
 */
class NewsMessages extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('News messages');
    }

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $list = new \XLite\Module\XC\News\View\ItemsList\Model\NewsMessage;
        $list->processQuick();
    }
}
