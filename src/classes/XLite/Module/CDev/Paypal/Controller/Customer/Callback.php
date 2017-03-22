<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

use \XLite\Module\CDev\Paypal;

/**
 * Checkout controller
 */
class Callback extends \XLite\Controller\Customer\Callback implements \XLite\Base\IDecorator
{
    protected $ignore = false;

    /**
     * @var \XLite\Model\Payment\Transaction
     */
    protected $transaction;

    /**
     * Trigger IPN resending by error response.
     */
    public function sendPaypalConflictResponse()
    {        
        $this->setSuppressOutput(true);
        $this->set('silent', true);
        
        header('HTTP/1.1 409 Conflict', true, 409);
        header('Status: 409 Conflict');
        header('X-Robots-Tag: noindex, nofollow');

        die();
    }
}
