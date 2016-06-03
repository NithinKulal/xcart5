<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\OgoneEcommerce\View\Payment;

/**
 * Payment method page
 */
class Method extends \XLite\View\Payment\Method implements \XLite\Base\IDecorator
{
    /**
     * Return Sign Up URL
     *
     * @return string
     */
    protected function getOgoneSignupURL()
    {
        return 'http://payment-services.ingenico.com/';
    }
}
