<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\StickyPanel\ItemsList;

/**
 * Reviews items list's sticky panel
 */
class Review extends \XLite\View\StickyPanel\ItemsListForm
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
                'label'      => 'Approve',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-check state-on',
            ),
            'XLite\Module\XC\Reviews\View\Button\Admin\ApproveSelectedReviews'
        );

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Reject',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-check state-off',
            ),
            'XLite\Module\XC\Reviews\View\Button\Admin\UnapproveSelectedReviews'
        );

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Delete',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-trash-o',
            ),
            'XLite\Module\XC\Reviews\View\Button\Admin\DeleteSelectedReviews'
        );

        return $list;
    }

    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['export'] = $this->getWidget(
            array(),
            'XLite\Module\XC\Reviews\View\Button\ItemsExport\Reviews'
        );
        return $list;
    }
}
