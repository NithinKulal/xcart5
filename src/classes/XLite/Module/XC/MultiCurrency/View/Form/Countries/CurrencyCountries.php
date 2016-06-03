<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\Form\Countries;

/**
 * Currency management page form
 */
class CurrencyCountries extends \XLite\View\Form\AForm
{
    /**
     * Get default target
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'currency_countries';
    }

    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        return parent::getDefaultParams() + array(
            'active_currency_id'   => \XLite\Core\Request::getInstance()->active_currency_id
        );
    }
}