<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\Controller\Customer;

/**
 * Checkout controller
 */
class Callback extends \XLite\Controller\Customer\Callback implements \XLite\Base\IDecorator
{
    /**
     * Trigger IPN resending by error response.
     */
    public function sendStripeConflictResponse()
    {        
        $this->setSuppressOutput(true);
        $this->set('silent', true);
        
        header('HTTP/1.1 409 Conflict', true, 409);
        header('Status: 409 Conflict');
        header('X-Robots-Tag: noindex, nofollow');

        die();
    }
}
