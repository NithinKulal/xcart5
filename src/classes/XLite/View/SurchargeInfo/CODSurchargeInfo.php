<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\SurchargeInfo;

/**
 * CODSurchargeInfo
 * @ListChild (list="checkout.review.surcharge.info", zone="customer", weight="10")
 */
class CODSurchargeInfo extends \XLite\View\SurchargeInfo\ASurchargeInfo
{
    /**
     * Return file name for the center part template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'checkout/steps/review/parts/items.modifiers.info.twig';
    }

    /**
     * Return true if COD payment method is selected
     *
     * @return boolean
     */
    protected function isCODSelected()
    {
        $result = false;

        $paymentMethod = $this->getCart()->getPaymentMethod();

        if ($paymentMethod) {
            $processor = $paymentMethod->getProcessor();

            $result = $processor && $processor->isCOD();
        }

        return $result;
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $surcharge = $this->getParam(static::PARAM_SURCHARGE);

        return $surcharge['code'] == 'SHIPPING'
            && $this->isCODSelected();
    }
}
