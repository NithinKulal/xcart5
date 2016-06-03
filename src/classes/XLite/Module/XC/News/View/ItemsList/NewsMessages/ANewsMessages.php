<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\View\ItemsList\NewsMessages;

/**
 * News message list view (absrtact)
 */
abstract class ANewsMessages extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Returns a list of CSS classes (separated with a space character) to be attached to the items list
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' news-messages';
    }

    /**
     * Return name of the base widgets list
     *
     * @return string
     */
    protected function getListName()
    {
        return parent::getListName() . '.newsMessages.customer';
    }

    /**
     * getJSHandlerClassName
     *
     * @return string
     */
    protected function getJSHandlerClassName()
    {
        return 'NewsMessagesItemsList';
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'list';
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
        $cnd->{\XLite\Module\XC\News\Model\Repo\NewsMessage::P_ORDER_BY} = array('n.date', 'desc');
        $cnd->{\XLite\Module\XC\News\Model\Repo\NewsMessage::SEARCH_ENABLED} = true;

        return \XLite\Core\Database::getRepo('XLite\Module\XC\News\Model\NewsMessage')->search($cnd, $countOnly);
    }
}
