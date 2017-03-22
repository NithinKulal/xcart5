<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\Controller\Customer;

use XLite\Module\XC\MultiCurrency\Core\MultiCurrency;

/**
 * Change customer currency
 */
class ChangeCurrency extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Do action 'update'
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneBy(
            array(
                'code' => \XLite\Core\Request::getInstance()->country_code
            )
        );

        $changeCountry = isset($country)
            && $country->getEnabled();

        $currency = \XLite\Core\Database::getRepo('XLite\Module\XC\MultiCurrency\Model\ActiveCurrency')
            ->getCurrencyByCode(\XLite\Core\Request::getInstance()->currency_code);

        $changeCurrency = isset($currency)
            && $currency->getEnabled();

        if ($changeCountry) {
            MultiCurrency::getInstance()->setSelectedCountry($country);
        }

        if ($changeCurrency) {
            MultiCurrency::getInstance()->setSelectedCurrency($currency->getCurrency());
            
            if ($this->getCart()) {
                $this->getCart()->updateMultiCurrency(
                    MultiCurrency::getInstance()->getSelectedMultiCurrency()
                );

                if ($this->getCart()->isPersistent()) {
                    \XLite\Core\Database::getEM()->flush($this->getCart());
                }
            }
        }

        $this->doActionChangeLanguage();
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        $this->setReturnURL($this->getReferrerURL());
    }
}