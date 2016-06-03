<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\View\StickyPanel;

/**
 * Panel form items list-based form
 */
class Options extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['addons-list'] = $this->getWidget(
            array(
                \XLite\View\Button\AButton::PARAM_STYLE               => 'action addons-list-back-button',
                \XLite\View\Button\BackToModulesLink::PARAM_MODULE_ID => $this->getModuleId(),
            ),
            '\XLite\View\Button\BackToModulesLink'
        );

        return $list;
    }
}
