<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Module;

/**
 * Pager for the marketplace page
 */
class Install extends \XLite\View\Pager\Admin\Module\AModule
{
    /**
     * What's new title text
     */
    const WHATS_NEW_TITLE = 'What\'s new';

    /**
     * getItemsPerPageDefault
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return 15;
    }

    /**
     * Do not show pager on bottom
     *
     * @return boolean
     */
    protected function isVisibleBottom()
    {
        return true;
    }

    /**
     * Return current list name
     *
     * @return string
     */
    protected function getListName()
    {
        return 'install-modules.pager';
    }

    /**
     * Define the pager title
     *
     * @return string
     */
    protected function getPagerTitle()
    {
        $pagerTitle = $this->getModuleId()
            ? ''
            : parent::getPagerTitle();

        return $this->isLandingPage()
            ? static::t(static::WHATS_NEW_TITLE)
            : $pagerTitle;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->getModuleId();
    }

    /**
     * Define the pager bottom title
     *
     * @return string
     */
    protected function getPagerBottomTitle()
    {
        return '<a href="' . $this->buildURL('addons_list_marketplace') . '">' . static::t('View all modules') . '</a>';
    }

    /**
     * Return true if 'Items-per-page' selector is visible
     *
     * @return boolean
     */
    protected function isItemsPerPageVisible()
    {
        return false;
    }
}
