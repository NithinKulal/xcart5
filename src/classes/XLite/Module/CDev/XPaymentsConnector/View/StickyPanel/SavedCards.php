<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\StickyPanel;

/**
 * Saved cards list buttons (sticky panel) 
 */
class SavedCards extends \XLite\View\StickyPanel\ItemsListForm 
{
    /**
     * Check panel has more actions buttons
     *
     * @return boolean
     */
    protected function hasMoreActionsButtons()
    {
        return \XLite\Module\CDev\XPaymentsConnector\Core\ZeroAuth::getInstance()->allowZeroAuth();
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        $location = \XLite\Core\Converter::buildURL(
            'add_new_card',
            '',
            array(
                'profile_id' => \XLite\Core\Request::getInstance()->profile_id,
            )
        );

        $list['add_new_card'] = $this->getWidget(
            array(
                \XLite\View\Button\AButton::PARAM_STYLE    => 'always-enabled',
                \XLite\View\Button\AButton::PARAM_LABEL    => 'Add new credit card',
                \XLite\View\Button\AButton::PARAM_DISABLED => false,
                \XLite\View\Button\Link::PARAM_LOCATION    => $location,
            ),
            'XLite\View\Button\Link'
        );

        return $list;
    }
}
