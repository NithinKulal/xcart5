<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Password recovery controller
 * TODO: full refactoring is needed
 */
class RecoverPassword extends \XLite\Controller\Admin\AAdmin
{
    // Expiration time of the password reset key
    const PASSWORD_RESET_KEY_EXP_TIME = 3600;

    /**
     * params
     *
     * @var string
     */
    protected $params = array('target', 'mode', 'email', 'link_mailed');

    /**
     * getAccessLevel
     *
     * @return integer
     */
    public function getAccessLevel()
    {
        return \XLite\Core\Auth::getInstance()->getCustomerAccessLevel();
    }

    /**
     * Check - is current place public or not
     *
     * @return boolean
     */
    protected function isPublicZone()
    {
        return 'recover_password' == \XLite\Core\Request::getInstance()->target;
    }

    /**
     * doActionRecoverPassword
     *
     * @return void
     */
    protected function doActionRecoverPassword()
    {
        // show recover message if email is valid
        if ($this->requestRecoverPassword($this->get('email'))) {
            $this->setReturnURL(
                $this->buildURL(
                    'recover_password',
                    '',
                    array(
                        'mode'        => 'recoverMessage',
                        'email'       => $this->get('email'),
                    )
                )
            );
        } else {
            $this->setReturnURL($this->buildURL('recover_password', '', array('valid' => 0)));
            \XLite\Core\TopMessage::addError('There is no user with specified email address');
        }
    }

    /**
     * doActionConfirm
     *
     * @return void
     */
    protected function doActionConfirm()
    {
        if (!is_null($this->get('email')) && \XLite\Core\Request::getInstance()->request_id) {
            if ($this->doPasswordRecovery($this->get('email'), \XLite\Core\Request::getInstance()->request_id)) {
                \XLite\Core\TopMessage::addInfo(
                    'Please create a new password'
                );
                
                $this->setReturnURL(
                    $this->buildURL('login')
                );
            }
        }
    }

    /**
     * requestRecoverPassword
     *
     * @param mixed $email Email
     *
     * @return boolean
     */
    protected function requestRecoverPassword($email)
    {
        $result = false;

        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($email);

        if (
            isset($profile)
            && $profile->isAdmin()
        ) {
            if (
                '' == $profile->getPasswordResetKey()
                || 0 == $profile->getPasswordResetKeyDate()
                || \XLite\Core\Converter::time() > $profile->getPasswordResetKeyDate()
            ) {
                // Generate new 'password reset key'
                $profile->setPasswordResetKey($this->generatePasswordResetKey());
                $profile->setPasswordResetKeyDate(\XLite\Core\Converter::time() + static::PASSWORD_RESET_KEY_EXP_TIME);

                $profile->update();
            }

            \XLite\Core\Mailer::sendRecoverPasswordRequest($profile->getLogin(), $profile->getPasswordResetKey());

            $result = true;
        }

        return $result;
    }

    /**
     * recoverPassword
     *
     * @param mixed $email     Email
     * @param mixed $requestID Request ID
     *
     * @return boolean
     */
    protected function doPasswordRecovery($email, $requestID)
    {
        $result = false;

        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($email);

        if (
            !isset($profile)
            || !$profile->isAdmin()
        ) {
            \XLite\Core\TopMessage::addError('There is no user with specified email address');

        } elseif (
            $profile->getPasswordResetKey() != $requestID
            || \XLite\Core\Converter::time() > $profile->getPasswordResetKeyDate()
        ) {
            \XLite\Core\TopMessage::addError('Your "Password reset key" has expired. Please enter the email address associated with your user account to get a new "Password reset key".');

            $profile->setPasswordResetKey('');
            $profile->setPasswordResetKeyDate(0);

            $profile->update();

        } else {
            $pass = generate_code();

            $profile->setPassword(md5($pass));
            $profile->setForceChangePassword(true);
            $profile->setPasswordResetKey('');
            $profile->setPasswordResetKeyDate(0);

            $result = $profile->update();

            if ($result) {
                \XLite\Core\Auth::getInstance()->loginProfile($profile);
            }
        }

        return $result;
    }

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return false;
    }

    /**
     * Generates password reset key
     *
     * @return string
     */
    protected function generatePasswordResetKey()
    {
        $result = \XLite\Core\Auth::encryptPassword(microtime(), \XLite\Core\Auth::DEFAULT_HASH_ALGO);

        if (
            !empty($result)
            && 0 === strpos($result, \XLite\Core\Auth::DEFAULT_HASH_ALGO)
        ) {
            $result = substr($result, 7);
        }

        return $result;
    }
}
