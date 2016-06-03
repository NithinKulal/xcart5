<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Pager\Admin\Module;

/**
 * Abstract pager class for the Modules list widget
 */
abstract class AModule extends \XLite\View\Pager\Admin\AAdmin
{
    const PARAM_CLEAR_PAGER = 'clearPager';

    /**
     * Register CSS files to include
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'items_list/module/pager/css/style.css';

        return $list;
    }

    /**
     * Return CSS classes to use in parent widget of pager
     *
     * @return string
     */
    public function getCSSClasses()
    {
        return parent::getCSSClasses() . ' addons-pager';
    }

    protected function getClearPagerDefault()
    {
        return (bool)\XLite\Core\Request::getInstance()->{static::PARAM_CLEAR_PAGER};
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_CLEAR_PAGER => new \XLite\Model\WidgetParam\TypeBool(
                'Clear pager',
                $this->getClearPagerDefault(),
                false
            ),
        );
    }

    /**
     * Return ID of the current page
     *
     * @return integer
     */
    protected function getPageId()
    {
        if ($this->getParam(static::PARAM_CLEAR_PAGER)) {
            $this->currentPageId = 1;
        }

        return parent::getPageId();
    }

    /**
     * Return current tag
     *
     * @return string
     */
    protected function getTag()
    {
        return \XLite\Core\Request::getInstance()->tag;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/module/pager/body.twig';
    }

    /**
     * Remove the standard pager placing
     *
     * @return boolean
     */
    protected function isVisibleBottom()
    {
        return false;
    }

    /**
     * Define the pager title
     *
     * @return string
     */
    protected function getPagerTitle()
    {
        return $this->getItemsTotal() . ' ' . static::t('modules');
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->filterRequestParams();
    }

    /**
     * Filter request parameters
     *
     * @return void
     */
    protected function filterRequestParams()
    {
        $filter = $this->getFilterRequestParams();

        if ($filter) {
            foreach ($this->requestParams as $key => $paramName) {
                if (in_array($paramName, $filter)) {
                    unset($this->requestParams[$key]);
                }
            }
        }
    }

    /**
     * Get list of keys to exclude from request parameters
     *
     * @return array
     */
    protected function getFilterRequestParams()
    {
        return array(static::PARAM_ITEMS_PER_PAGE);
    }

    /**
     * Return number of pages to display
     *
     * @return integer
     */
    protected function getPagesPerFrame()
    {
        return 5;
    }

    /**
     * Get items per page ranges list
     *
     * @return array
     */
    protected function getItemsPerPageRanges()
    {
        return array(10, 25, 50, 75, 100);
    }

    /**
     * Return true if range is selected
     *
     * @param integer $range Range to check
     *
     * @return boolean
     */
    protected function isRangeSelected($range)
    {
        return $range == $this->getItemsPerPage();
    }

    /**
     * isVisible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return true;
    }
}
