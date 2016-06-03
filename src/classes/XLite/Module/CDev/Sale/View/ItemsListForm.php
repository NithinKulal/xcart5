<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\View;

/**
 * Items list form button
 */
abstract class ItemsListForm extends \XLite\View\StickyPanel\Product\Admin\Search implements \XLite\Base\IDecorator
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
                'label'      => 'Put up for sale',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-percent state-on',
            ),
            'XLite\Module\CDev\Sale\View\SaleSelectedButton'
        );

        $list[] = $this->getWidget(
            array(
                'disabled'   => true,
                'label'      => 'Cancel sale',
                'style'      => 'more-action',
                'icon-style' => 'fa fa-percent state-off',
            ),
            'XLite\Module\CDev\Sale\View\CancelSaleSelectedButton'
        );

        return $list;
    }
}
