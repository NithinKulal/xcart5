<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Model;

/**
 * Infinity pager
 */
class Infinity extends \XLite\View\Pager\Admin\Model\AModel
{
    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[self::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR]->setValue(false);
        $this->widgetParams[self::PARAM_ONLY_PAGES]->setValue(true);
    }

    /**
     * Get items per page (default)
     *
     * @return integer
     */
    protected function getItemsPerPageDefault()
    {
        return $this->getItemsPerPageMax();
    }

    /**
     * Return maximal possible items number per page
     *
     * @return integer
     */
    protected function getItemsPerPageMax()
    {
        return 1000000000;
    }

    /**
     * getItemsPerPage
     *
     * @return integer
     */
    public function getItemsPerPage()
    {
        return $this->getItemsPerPageMax();
    }

    /**
     * Return ID of the current page
     *
     * @return integer
     */
    protected function getPageId()
    {
        return 0;
    }

    /**
     * Get direcory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'pager/model';
    }

}
