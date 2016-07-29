<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\StickyPanel;

/**
 * Payment methods list buttons (sticky panel) 
 */
class PaymentMethods extends \XLite\View\StickyPanel\ItemsListForm 
{
    /**
     * Check panel has more actions buttons
     *
     * @return boolean
     */
    protected function hasMoreActionsButtons()
    {
        return true;
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        $list['import'] = $this->getWidget(
            array(
                \XLite\View\Button\AButton::PARAM_STYLE    => 'always-enabled',
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Re-import payment methods',
                \XLite\View\Button\AButton::PARAM_DISABLED => false
            ),
            'XLite\Module\CDev\XPaymentsConnector\View\Button\PaymentMethods\Import'
        );

        $list['add_new'] = $this->getWidget(
            array(
                \XLite\View\Button\AButton::PARAM_STYLE     => 'action link always-enabled',
                \XLite\View\Button\AButton::PARAM_LABEL     => 'Add new payment method',
                \XLite\View\Button\AButton::PARAM_DISABLED  => false,
                \XLite\View\Button\Link::PARAM_BLANK        => true,
            ),
            'XLite\Module\CDev\XPaymentsConnector\View\Button\PaymentMethods\AddNew'
        );

        return $list;
    }
}
