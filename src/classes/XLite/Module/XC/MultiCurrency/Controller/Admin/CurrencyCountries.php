<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Controller\Admin;

/**
 * Currencies management page controller
 */
class CurrencyCountries extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Do action 'update'
     *
     * @return void
     */
    public function doActionUpdate()
    {
        $addCountries = array();
        $removeCountries = array();

        if (
            is_array(\XLite\Core\Request::getInstance()->data)
            && !empty(\XLite\Core\Request::getInstance()->data)
        ) {
            foreach (\XLite\Core\Request::getInstance()->data as $code => $value) {
                if ($value['enabled']) {
                    $addCountries[] = $code;
                } else {
                    $removeCountries[] = $code;
                }
            }
        }

        \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
            ->updateCountriesByCode(
                \XLite\Core\Request::getInstance()->active_currency_id,
                array(
                    'add'       => $addCountries,
                    'remove'    => $removeCountries
                )
            );
    }

    /**
     * Search labels
     *
     * @return void
     */
    protected function doActionSearch()
    {
        $search = array();
        $searchParams   = \XLite\Module\XC\MultiCurrency\View\ItemsList\Model\Country::getSearchParams();

        foreach ($searchParams as $modelParam => $requestParam) {
            if (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $search[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        $name = \XLite\Module\XC\MultiCurrency\View\ItemsList\Model\Country::getSessionCellName();
        \XLite\Core\Session::getInstance()->$name = $search;
    }
}