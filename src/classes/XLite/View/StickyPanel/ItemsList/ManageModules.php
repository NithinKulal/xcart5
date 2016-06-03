<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\ItemsList;

/**
 * Manage modules list's sticky panel
 */
class ManageModules extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();

        $list[] = $this->getWidget(
            array(),
            'XLite\View\Button\Addon\ManageModulesSelected'
        );

        return $list;
    }
}
