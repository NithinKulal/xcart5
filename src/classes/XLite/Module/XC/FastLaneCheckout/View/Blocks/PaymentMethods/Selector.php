<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Blocks\PaymentMethods;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
class Selector extends \XLite\View\Checkout\PaymentMethodsList
{
    /**
     * @return string
     */
    public function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/payment_methods/';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . 'selector.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'selector.twig';
    }

    protected function defineWidgetData()
    {
        return array(
            'required' => !$this->isPayedCart(),
            'methodId' => $this->getCart()->getPaymentMethodId(),
        );
    }

    protected function getWidgetData()
    {
        return json_encode($this->defineWidgetData());
    }
}
