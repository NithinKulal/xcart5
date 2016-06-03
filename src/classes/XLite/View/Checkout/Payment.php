<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Checkout;

/**
 * Payment template
 *
 * @ListChild (list="checkout.review.selected.placeOrder", weight="100")
 */
class Payment extends \XLite\View\AView
{
    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'checkout/steps/review/parts/payment.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/steps/review/parts/place_order.payment.twig';
    }

    /**
     * Get payment template
     *
     * @return string|void
     */
    protected function getPaymentTemplate()
    {
        $processor = $this->getProcessor();

        return $processor ? $processor->getInputTemplate() : null;
    }

    /**
     * Get payment processor
     *
     * @return \XLite\Model\Payment\Base\Processor
     */
    protected function getProcessor()
    {
        return $this->getCart()->getPaymentMethod()
            ? $this->getCart()->getPaymentMethod()->getProcessor()
            : null;
    }

}
