<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Go to checkout 
 */
class GoToCheckout extends \XLite\View\Button\Link
{
    /**
     * Get default label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Go to checkout';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_LOCATION]->setValue($this->buildURL('checkout'));
    }

    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return $this->getCart()->checkCart()
            ? 'regular-main-button checkout'
            : 'regular-main-button disabled add2cart-disabled checkout';
    }

}

