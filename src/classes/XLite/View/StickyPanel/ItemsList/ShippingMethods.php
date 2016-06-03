<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\StickyPanel\ItemsList;

/**
 * Shipping methods list's sticky panel
 */
class ShippingMethods extends \XLite\View\StickyPanel\ItemsListForm
{
    /**
     * Define additional buttons
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['shipping_methods'] = new \XLite\View\Button\SimpleLink(
            array(
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to shipping methods'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action shipping-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('shipping_methods'),
            )
        );

        return $list;
    }
}
