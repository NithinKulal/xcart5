<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp Exception
 */
class MailChimpException extends \XLite\Core\Exception
{
    const MAILCHIMP_NO_API_KEY_ERROR = 'Invalid MailChimp API key supplied.';

    /**
     * Construct the exception.
     *
     * @param string     $message  The Exception message to throw. OPTIONAL
     * @param integer    $code     The Exception code. OPTIONAL
     * @param \Exception $previous The previous exception used for the exception chaining. OPTIONAL
     *
     * @return void
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
