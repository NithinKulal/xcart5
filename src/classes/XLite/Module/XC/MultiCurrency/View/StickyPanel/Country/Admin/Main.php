<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MultiCurrency\View\StickyPanel\Country\Admin;

/**
 * Panel form items list-based form
 */
class Main extends \XLite\View\StickyPanel\Country\Admin\Main
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $return = parent::defineButtons();

        $return['backToList'] = $this->getBackToListButton();

        return $return;
    }

    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = \XLite\View\StickyPanel\ItemsListForm::defineAdditionalButtons();

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Delete',
                'style'      => 'more-action link list-action',
                'icon-style' => 'fa fa-trash-o',
            ),
            'XLite\View\Button\DeleteSelected'
        );

        return $list;
    }

    /**
     * Get approve button
     *
     * @return \XLite\View\Button\Regular
     */
    protected function getBackToListButton()
    {
        return $this->getWidget(
            array(
                'style'                                 => 'action always-enabled',
                'label'                                 => $this->getBackToListButtonLabel(),
                'disabled'                              => false,
                \XLite\View\Button\Link::PARAM_BTN_TYPE => 'regular-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('currencies')
            ),
            'XLite\View\Button\Link'
        );
    }

    /**
     * Defines the label for the approve button
     *
     * @return string
     */
    protected function getBackToListButtonLabel()
    {
        return static::t('Back to currencies list');
    }
}
