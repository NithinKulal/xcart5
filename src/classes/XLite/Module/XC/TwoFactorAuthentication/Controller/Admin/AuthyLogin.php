<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\Controller\Admin;

/**
 * Authy login
 */
class AuthyLogin extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(parent::defineFreeFormIdActions(), array('resend_token'));
    }
    
    /**
     * Is redirect needed
     *
     * @return boolean
     */
    public function isRedirectNeeded()
    {
        $result = parent::isRedirectNeeded();
        
        if ('authy_login' == $this->getTarget()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check - is current place public or not
     *
     * @return boolean
     */
    protected function isPublicZone()
    {
        return true;
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
                $this->redirect();
            }

            \XLite\Core\Database::getEM()->flush();
        }

        $sms = $authyCore->sendSMS();

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
    protected function  doActionLogin()
    {
        $token = \XLite\Core\Request::getInstance()->sms_token;

        $authyCore = \XLite\Module\XC\TwoFactorAuthentication\Core\Authy::getInstance();
        $verifyResult = $authyCore->verifyToken($token);
        if ($verifyResult) {
            $this->loginSessionProfile();
            $this->doRedirect();
        } else {
            \XLite\Core\TopMessage::addError(static::t('SMS code is invalid. Resend SMS code'));
        }
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
        if (isset($profile)) {
            \XLite\Core\Auth::getInstance()->loginProfile($profile);
        }

        $returnURL = $this->buildURL();
        $this->setReturnURL($returnURL);
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
