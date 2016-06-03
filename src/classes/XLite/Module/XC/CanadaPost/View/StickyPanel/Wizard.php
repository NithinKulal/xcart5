<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\View\StickyPanel;

/**
 * Wizard sticky panel
 */
class Wizard extends \XLite\View\StickyPanel\Model\AModel
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        $list['save'] = new \XLite\View\Button\Submit(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Register',
                \XLite\View\Button\AButton::PARAM_BTN_TYPE => 'regular-main-button',
                \XLite\View\Button\AButton::PARAM_STYLE    => 'action always-enabled',
            )
        );

        $list['shipping_methods'] = new \XLite\View\Button\SimpleLink(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to shipping methods'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action shipping-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('shipping_methods'),
            )
        );

        return $list;
    }

    /**
     * Defines the label for the save button
     *
     * @return string
     */
    protected function getSaveWidgetLabel()
    {
        return static::t('Register');
    }
}
