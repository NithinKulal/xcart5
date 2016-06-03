<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\State\Admin;

/**
 * Abstract state panel for admin interface
 */
abstract class AAdmin extends \XLite\View\StickyPanel\State\AState
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
                'label'      => 'Delete',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-trash-o',
            ),
            'XLite\View\Button\DeleteSelectedStates'
        );

        return $list;
    }
}
