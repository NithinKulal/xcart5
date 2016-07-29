<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\Button\PaymentMethods;

/**
 * Add new payment method
 */
class AddNew extends \XLite\View\Button\Link
{
    /**
     * Link to the payment methods page 
     *
     * @return string
     */
    protected function getLocationURL() 
    {
        return \XLite\Core\Config::getInstance()->CDev->XPaymentsConnector
            ->xpc_xpayments_url . 'admin.php?target=payment_confs';
    }

}

