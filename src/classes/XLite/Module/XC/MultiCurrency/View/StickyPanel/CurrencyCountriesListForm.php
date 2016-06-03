<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\StickyPanel;

/**
 * Panel form items list-based form
 */
class CurrencyCountriesListForm extends \XLite\View\StickyPanel\ItemForm
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $return = parent::defineButtons();

        $return['updateRates'] = $this->getUpdateRatesButton();

        return $return;
    }

    /**
     * Get approve button
     *
     * @return \XLite\View\Button\Regular
     */
    protected function getUpdateRatesButton()
    {
        return $this->getWidget(
            array(
                'style'                                     => 'action update-rates always-enabled',
                'label'                                     => $this->getUpdateRatesButtonLabel(),
                'disabled'                                  => false,
                \XLite\View\Button\AButton::PARAM_BTN_TYPE  => 'regular-button',
                \XLite\View\Button\Regular::PARAM_ACTION    => 'update_rates',
            ),
            'XLite\View\Button\Regular'
        );
    }

    /**
     * Defines the label for the approve button
     *
     * @return string
     */
    protected function getUpdateRatesButtonLabel()
    {
        return static::t('Update rates');
    }
}