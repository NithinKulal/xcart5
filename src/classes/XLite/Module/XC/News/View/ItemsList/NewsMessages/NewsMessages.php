<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\News\View\ItemsList\NewsMessages;

/**
 * News message items list for archive
 */
class NewsMessages extends \XLite\Module\XC\News\View\ItemsList\NewsMessages\ANewsMessages
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/News/news_messages/style.css';

        return $list;
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/News/news_messages/controller.js';

        return $list;
    }

    /**
     * Get data matrix 
     * 
     * @return array
     */
    protected function getMatrix()
    {
        $result = array();
        foreach ($this->getPageData() as $model) {
            $key = date('mY', $model->getDate());
            if (!isset($result[$key])) {
                $result[$key] = array(
                    'title' => date('F, Y', $model->getDate()),
                    'list' => array(),
                );
            }

            $result[$key]['list'][] = $model;
        }

        return $result;
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return '\XLite\Module\XC\News\View\Pager\NewsMessages';
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.center';
    }

    /**
     * Get widget templates directory
     * NOTE: do not use "$this" pointer here (see "getBody()" and "get[CSS/JS]Files()")
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/News/news_messages';
    }
}
