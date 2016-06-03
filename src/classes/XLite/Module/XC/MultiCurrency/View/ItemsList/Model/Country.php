<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\ItemsList\Model;

/**
 * Active currencies list
 */
class Country extends \XLite\View\ItemsList\Model\Country
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        $return = parent::defineColumns();

        unset($return['states']);

        $return['country'][static::COLUMN_CLASS] = '\XLite\View\FormField\Inline\Label';

        return $return;
    }

    /**
     * Get switcher field
     *
     * @return array
     */
    protected function getSwitcherField()
    {
        return array(
            'class' => 'XLite\Module\XC\MultiCurrency\View\FormField\Inline\Input\Checkbox\Switcher\CurrencyCountry',
            'name' => 'enabled',
            'params' => array(),
        );
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $return = parent::getCommonParams();

        $return['active_currency_id'] = \XLite\Core\Request::getInstance()->active_currency_id;

        return $return;
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $return = new \XLite\Core\CommonCell();

        foreach (static::getSearchParams() as $modelParam => $requestParam) {
            $return->$modelParam = is_string($this->getParam($requestParam))
                ? trim($this->getParam($requestParam))
                : $this->getParam($requestParam);

            $name = \XLite\Module\XC\MultiCurrency\View\ItemsList\Model\Country::getSessionCellName();
            \XLite\Core\Session::getInstance()->$name = array();
        }

        $return->{\XLite\Module\XC\MultiCurrency\Model\Repo\Country::P_ACTIVE_CURRENCY}
            = \XLite\Core\Request::getInstance()->active_currency_id;

        $return->{\XLite\Module\XC\MultiCurrency\Model\Repo\Country::P_ORDER_BY_ACTIVE_CURRENCY}
            = \XLite\View\ItemsList\AItemsList::SORT_ORDER_DESC;

        $return->{\XLite\Model\Repo\Country::P_ORDER_BY} = array(
            'translations.country',
            \XLite\View\ItemsList\AItemsList::SORT_ORDER_ASC
        );

        return $return;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\Module\XC\MultiCurrency\View\StickyPanel\Country\Admin\Main';
    }
}