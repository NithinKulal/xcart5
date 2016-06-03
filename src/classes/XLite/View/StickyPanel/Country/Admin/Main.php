<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\Country\Admin;

/**
 * Countries
 */
class Main extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineAdditionalButtons()
    {
        $list = parent::defineAdditionalButtons();

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Enable',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-power-off state-on',
            ),
            'XLite\View\Button\EnableSelected'
        );
        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Disable',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-power-off state-off',
            ),
            'XLite\View\Button\DisableSelected'
        );

        return $list;
    }

}

