<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\Form;

/**
 * Test module form
 *
 */
class PaymentMethods extends \XLite\Module\CDev\XPaymentsConnector\View\Form\Settings
{
    /**
     * Get default action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_payment_methods';
    }
}
