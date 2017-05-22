<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Controller\Customer;

/**
 * Authy login
 */
class AuthyLogin extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Enter SMS code');
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (!isset(\XLite\Core\Session::getInstance()->preauth_authy_profile_id)) {
            $returnURL = $this->buildURL('login');
            $this->setReturnURL($returnURL);
            $this->redirect();
        }

        $authyCore = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();

        if (!$authyCore->getAuthyIdFromSession()) {
            $registeredAuthyId = $authyCore->registerAuthyForSessionProfile();
            if (empty($registeredAuthyId)) {
                $this->loginSessionProfile();
            }

            \XLite\Core\Database::getEM()->flush();
        }

        $sms = $authyCore->sendSMS();

        static::log(array('send_token' => $sms));

        if (!$sms->ok()) {
            $label = 'Authy:' . $authyCore->getResponseError($sms);
            \XLite\Core\TopMessage::addError($label);
        }
    }

    /**
     * Login action
     *
     * @return void
     */
    protected function doActionLogin()
    {
        $token = \XLite\Core\Request::getInstance()->sms_token;

        $authyCore = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();
        $verifyResult = $authyCore->verifyToken($token);
        if ($verifyResult) {
            $this->loginSessionProfile();
        } else {
            $this->addTokenFailedMessage();
        }

        static::log(array('verify_result' => $verifyResult));
    }

    /**
     * Resend sms token action
     *
     * @return void
     */
    protected function doActionResendToken()
    {
        $this->setSilenceClose(true);

        $authyCore = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();
        $sms = $authyCore->sendSMS();

        static::log(array('resend_token' => $sms));

        if (!$sms->ok()) {
            $label = 'Authy:' . $authyCore->getResponseError($sms);
            \XLite\Core\TopMessage::addError($label);
        }
    }

    /**
     * Login session profile
     *
     * @return void
     */
    protected function loginSessionProfile()
    {
        $profileId = \XLite\Core\Session::getInstance()->preauth_authy_profile_id;
        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->find($profileId);

        $this->setReturnURL($this->buildURL());

        if (isset($profile)) {
             \XLite\Core\Auth::getInstance()->loginProfile($profile);

            if (\XLite\Core\Request::getInstance()->preReturnURL) {
                $url = preg_replace(
                    '/' . preg_quote(\XLite\Core\Session::getInstance()->getName()) . '=([^&]+)/',
                    '',
                    \XLite\Core\Request::getInstance()->preReturnURL
                );
                $this->setReturnURL($url);
            }

            $profileCart = $this->getCart();
            if (!$this->getReturnURL()) {
                $url = $profileCart->isEmpty()
                    ? \XLite\Core\Converter::buildURL()
                    : \XLite\Core\Converter::buildURL('cart');
                $this->setReturnURL($url);
            }

            $this->setHardRedirect();

            // We merge the logged in cart into the session cart
            $profileCart->login($profile);
            \XLite\Core\Database::getEM()->flush();

            if ($profileCart->isPersistent()) {
                $this->updateCart();
                \XLite\Core\Event::getInstance()->exclude('updateCart');
            }
        }

    }

    /**
     * Add top message if log in is failed
     *
     * @return void
     */
    protected function addTokenFailedMessage()
    {
        \XLite\Core\TopMessage::addError(static::t('SMS code is invalid. Resend SMS code'));
        \XLite\Core\Event::invalidForm('login-form', static::t('Invalid SMS code'));

    }

    /**
     * Logging the data under AuthyLogin
     * Available if developer_mode is on in the config file
     *
     * @param mixed $data Log data
     *
     * @return void
     */
    protected static function log($data)
    {
        if (LC_DEVELOPER_MODE) {
            \XLite\Logger::logCustom('AuthyLogin', $data);
        }
    }
}
