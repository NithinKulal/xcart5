<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core\Track;

use XLite\Core\Auth;
use XLite\Module\XC\Concierge\Core\ATrack;

class LoginFailed extends ATrack
{
    /**
     * @var string
     */
    protected $code;

    /**
     * @param string $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    public function getEvent()
    {
        return 'Login Failed';
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getProperties()
    {
        return [
            'code'    => $this->getCode(),
            'message' => $this->getMessage(),
        ];
    }

    /**
     * @return string
     */
    protected function getMessage()
    {
        switch ($this->getCode()) {
            case Auth::RESULT_INVALID_SECURE_HASH:
                return 'Trying to log in using an invalid secure hash string';

            case Auth::RESULT_PASSWORD_NOT_EQUAL:
                return 'Password is wrong';

            case Auth::RESULT_LOGIN_IS_LOCKED:
                return 'Account is locked';

            case Auth::RESULT_PROFILE_IS_ANONYMOUS:
                return 'Account is anonymous';

            default:
                return 'Unknown reason';
        }
    }
}
