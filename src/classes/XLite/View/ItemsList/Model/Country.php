<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model;

/**
 * Countries items list
 */
class Country extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Search parameter name
     */
    const PARAM_SUBSTRING = 'substring';

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'items_list/model/table/countries/style.css';

        return $list;
    }

    /**
     * Should itemsList be wrapped with form
     *
     * @return boolean
     */
    protected function wrapWithFormByDefault()
    {
        return true;
    }

    /**
     * Get wrapper form target
     *
     * @return array
     */
    protected function getFormTarget()
    {
        return 'countries';
    }

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\XLite\View\SearchPanel\Countries\Main';
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'country' => array (
                static::COLUMN_NAME   => \XLite\Core\Translation::lbl('Country'),
                static::COLUMN_CLASS  => '\XLite\View\FormField\Inline\Input\Text',
                static::COLUMN_PARAMS => array('required' => true),
                static::COLUMN_ORDERBY  => 100,
            ),
            'code' => array (
                static::COLUMN_NAME      => \XLite\Core\Translation::lbl('Code'),
                static::COLUMN_ORDERBY  => 200,
            ),
            'states' => array (
                static::COLUMN_NAME     => \XLite\Core\Translation::lbl('States'),
                static::COLUMN_TEMPLATE => false,
                static::COLUMN_ORDERBY  => 300,
            ),
        );
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return '\XLite\Model\Country';
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return true;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return true;
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' countries';
    }

    /**
     * Get pager parameters
     *
     * @return array
     */
    protected function getPagerParams()
    {
        $params = parent::getPagerParams();

        $params[\XLite\View\Pager\APager::PARAM_ITEMS_PER_PAGE] = 50;

        return $params;
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\Country::P_SUBSTRING  => static::PARAM_SUBSTRING,
        );
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
            static::PARAM_SUBSTRING  => new \XLite\Model\WidgetParam\TypeString('Country name pattern', ''),
        );
    }

    /**
     * Define so called "request" parameters
     *
     * @return void
     */
    protected function defineRequestParams()
    {
        parent::defineRequestParams();

        $this->requestParams = array_merge($this->requestParams, static::getSearchParams());
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $result->$modelParam = is_string($this->getParam($requestParam))
                ? trim($this->getParam($requestParam))
                : $this->getParam($requestParam);
        }

        $result->{\XLite\Model\Repo\Country::P_ORDER_BY} = array('translations.country', 'ASC');

        return $result;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\Country\Admin\Main';
    }

}
