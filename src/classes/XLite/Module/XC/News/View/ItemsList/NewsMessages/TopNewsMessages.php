<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\View\ItemsList\NewsMessages;

/**
 * Top news messages items list
 */
class TopNewsMessages extends \XLite\Module\XC\News\View\ItemsList\NewsMessages\ANewsMessages
{

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/News/top_news_messages/controller.js';

        return $list;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\View\Pager\Infinity';
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.top';
    }

    /**
     * Get widget templates directory
     * NOTE: do not use "$this" pointer here (see "getBody()" and "get[CSS/JS]Files()")
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/News/top_news_messages';
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $cnd->{\XLite\Module\XC\News\Model\Repo\NewsMessage::P_LIMIT} = array(0, \XLite\Core\Config::getinstance()->XC->News->top_news_length);

        return parent::getData($cnd, $countOnly);
    }

}
