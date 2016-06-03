<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\StickyPanel\ItemsList;

/**
 * Product variant items list's sticky panel
 */
class ProductVariant extends \XLite\View\StickyPanel\ItemsListForm
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
                'style'      => 'more-action',
                'icon-style' => 'fa fa-trash-o',
            ),
            'XLite\Module\XC\ProductVariants\View\Button\DeleteSelectedVariants'
        );

        return $list;
    }
}
