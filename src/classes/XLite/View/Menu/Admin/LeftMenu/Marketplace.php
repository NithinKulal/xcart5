<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Menu\Admin\LeftMenu;

/**
 * Marketplace main node
 */
class Marketplace extends \XLite\View\Menu\Admin\LeftMenu\ANode
{
    /**
     * Returns CSS files
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
     * Return widget directory
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/marketplace';
    }

    /**
     * Return list name
     *
     * @return string
     */
    protected function getListName()
    {
        return 'menu.info' . $this->getParam(static::PARAM_LIST);
    }

    /**
     * Get label
     *
     * @return string
     */
    protected function getLabel()
    {
        return '&nbsp;';
    }

    /**
     * Return CSS class for the link item
     *
     * @return string
     */
    protected function getCSSClass()
    {
        return parent::getCSSClass() . ' notification';
    }
}
