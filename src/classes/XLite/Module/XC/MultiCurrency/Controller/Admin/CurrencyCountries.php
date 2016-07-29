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
     * Do action 'add_currency'
     *
     * @return void
     */
    public function doActionAddCurrencyCountries()
    {
        $data = \XLite\Core\Request::getInstance()->getData();

        if (isset($data['currency_country_id'])
            && !empty($data['currency_country_id'])
        ) {
            $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency');
            if (is_array($data['currency_country_id'])) {
                $repo->updateCountriesByCode(
                    \XLite\Core\Request::getInstance()->active_currency_id,
                    array(
                        'add'       => $data['currency_country_id'],
                        'remove'    => []
                    )
                );
            } else {
                $repo->addCountryByCode(
                    \XLite\Core\Request::getInstance()->active_currency_id,
                    $data['currency_country_id']
                );
            }
        }
    }

    /**
     * Do action 'update'
     *
     * @return void
     */
    public function doActionUpdateItemsList()
    {
        $removeCountries = array();

        $data = \XLite\Core\Request::getInstance()->getData();

        if ($data) {
            $prefix = 'delete';

            if (isset($data[$prefix]) && is_array($data[$prefix]) && $data[$prefix]) {
                foreach ($data[$prefix] as $id => $allow) {
                    if ($allow) {
                        $removeCountries[] = $id;
                    }
                }
            }

            \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
                ->updateCountriesByCode(
                    \XLite\Core\Request::getInstance()->active_currency_id,
                    array(
                        'add'       => [],
                        'remove'    => $removeCountries
                    )
                );
        }
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
