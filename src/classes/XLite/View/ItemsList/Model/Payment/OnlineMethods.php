<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Model\Payment;

/**
 * Methods items list
 */
class OnlineMethods extends \XLite\View\ItemsList\Model\Table
{
    /**
     * Widget params
     */
    const PARAM_SUBSTRING = 'substring';
    const PARAM_COUNTRY   = 'country';

    /**
     * Get search panel widget class
     *
     * @return string
     */
    protected function getSearchPanelClass()
    {
        return '\XLite\View\SearchPanel\Payment\Admin\Main';
    }

    /**
     * Check - table header is visible or not
     *
     * @return boolean
     */
    protected function isHeaderVisible()
    {
        return true;
    }

    /**
     * Return specific CSS class for dialog wrapper
     *
     * @return string
     */
    protected function getDialogCSSClass()
    {
        return parent::getDialogCSSClass() . ' payment-gateways';
    }

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            array(
                'payment_method_selection',
            )
        );
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/' . \XLite\View\ItemsList\Model\Table::getPageBodyDir() . '/controller.js';
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/controller.js';

        return $list;
    }

    /**
     * Get default payment method country
     *
     * @return string
     */
    public static function getDefaultCountry()
    {
        return null;
    }

    /**
     * Get pager class
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Admin\Model\PaymentMethod\Table';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SUBSTRING => new \XLite\Model\WidgetParam\TypeString(
                'Substring', ''
            ),
            static::PARAM_COUNTRY => new \XLite\Model\WidgetParam\TypeString(
                'Country code', static::getDefaultCountry()
            ),
        );
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array(
            'name' => array(
                static::COLUMN_NAME    => static::t('Payment method'),
                static::COLUMN_ORDERBY  => 100,
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
        return 'XLite\Model\Payment\Method';
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * Return dir which contains the page body template
     *
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'payment/online_methods';
    }

    // }}}

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' payment-methods';
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $result = parent::getSearchCondition();

        $result->{\XLite\Model\Repo\Payment\Method::P_ORDER_BY_FORCE} = array(
            'm.adminOrderby, translations.name', 'asc'
        );
        $result->{\XLite\Model\Repo\Payment\Method::P_TYPE} = array(
            \XLite\Model\Payment\Method::TYPE_ALLINONE,
            \XLite\Model\Payment\Method::TYPE_CC_GATEWAY,
            \XLite\Model\Payment\Method::TYPE_ALTERNATIVE,
        );

        $result->{\XLite\Model\Repo\Payment\Method::P_EX_COUNTRY} = trim($this->getParam(static::PARAM_COUNTRY));

        return $result;
    }

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return false;
    }

    /**
     * Return search parameters.
     *
     * @return array
     */
    static public function getSearchParams()
    {
        return array(
            \XLite\Model\Repo\Payment\Method::P_NAME        => static::PARAM_SUBSTRING,
            \XLite\Model\Repo\Payment\Method::P_COUNTRY     => static::PARAM_COUNTRY,
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
     * Return "empty list" catalog
     *
     * @return string
     */
    protected function getEmptyListDir()
    {
        return parent::getEmptyListDir() . '/' . $this->getPageBodyDir();
    }
}
