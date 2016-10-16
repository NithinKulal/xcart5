<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Core version
 *
 * @ListChild (list="admin.main.page.header", weight="300", zone="admin")
 */
class QueryMarketplace extends \XLite\View\AView
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/controller.js';

        return $list;
    }

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/body.twig';
    }

    /**
     * Return widget default directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'query_marketplace';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isAdmin()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Get commented widget data
     *
     * @return array
     */
    protected function getCommentedData()
    {
        $actions = \XLite\Core\Marketplace::getInstance()->getActionsForGetDataset();

        return array(
            'hasPendingActions' => array_keys($actions),
            'parentTarget'      => $this->getTarget(),
        );
    }
}
