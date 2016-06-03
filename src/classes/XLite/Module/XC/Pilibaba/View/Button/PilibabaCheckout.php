<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View\Button;

use XLite\Module\XC\Pilibaba;

/**
 * Express Checkout base button
 *
 * @ListChild (list="minicart.horizontal.buttons",              weight="1000")
 * @ListChild (list="cart.panel.totals",                        weight="1000")
 * @ListChild (list="add2cart_popup.item",                      weight="1000")
 */
class PilibabaCheckout extends \XLite\View\Button\Link
{
    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getCart()
            && Pilibaba\Main::getPaymentMethod()->isEnabled();
    }

   /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Pilibaba/checkout-btn.css';

        return $list;
    }

    /**
     * Get CSS class name
     *
     * @return string
     */
    protected function getClass()
    {
        return 'pilibaba-checkout-button';
    }

    /**
     * Get default label
     *
     * @return string
     */
    protected function getButtonLabel()
    {
        return static::t('Pilibaba');
    }

    /**
     * defineWidgetParams
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_LOCATION] = new \XLite\Model\WidgetParam\TypeString(
            'Redirect to',
            $this->buildURL('checkout', 'start_pilibaba_checkout')
        );
    }
}
