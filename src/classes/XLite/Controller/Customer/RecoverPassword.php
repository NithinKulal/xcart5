<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Password recovery controller
 */
class RecoverPassword extends \XLite\Controller\Customer\ACustomer
{
    // Expiration time of the password reset key
    const PASSWORD_RESET_KEY_EXP_TIME = 3600;

    /**
     * params
     *
     * @var string
     */
    protected $params = array('target', 'email');

    /**
     * Add the base part of the location path
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('Help zone'));
    }

    /**
     * Common method to determine current location
     *
     * @return array
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Forgot password?');
    }

    /**
     * doActionRecoverPassword
     *
     * @return void
     */
    protected function doActionRecoverPassword()
    {
        if ($this->requestRecoverPassword($this->get('email'))) {
            \XLite\Core\TopMessage::addInfo(
                'The confirmation URL link was mailed to email',
                array('email' => $this->get('email'))
            );

            if ($this->isAJAX()) {
                \XLite\Core\Event::recoverPasswordSent(array('email' => $this->get('email')));
                $this->setSilenceClose();

            } else {
                $this->setReturnURL($this->buildURL());
            }

        } else {
            $this->setReturnURL($this->buildURL('recover_password'));

            if (!$this->isAJAX()) {
                \XLite\Core\TopMessage::addError('There is no user with specified email address');
            }

            \XLite\Core\Event::invalidElement('email', static::t('There is no user with specified email address'));
        }
    }

    /**
     * doActionConfirm
     *
     * @return void
     */
    protected function doActionConfirm()
    {
        if (
            $this->get('email')
            && \XLite\Core\Request::getInstance()->request_id
            && $this->doPasswordRecovery($this->get('email'), \XLite\Core\Request::getInstance()->request_id)
        ) {
            \XLite\Core\TopMessage::addInfo(
                'Please create a new password'
            );
            $this->setReturnURL($this->buildURL());
            \XLite\Core\Event::recoverPasswordDone(array('email' => $this->get('email')));
        }
    }

    /**
     * Sent Recover password mail
     *
     * @param string $email Email
     *
     * @return boolean
     */
    protected function requestRecoverPassword($email)
    {
        $result = false;

        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($email);

        if (
            isset($profile)
            && !$profile->isAdmin()
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
     * Recover password
     *
     * @param string $email     Profile email
     * @param string $requestID Request ID
     *
     * @return boolean
     */
    protected function doPasswordRecovery($email, $requestID)
    {
        $result = false;

        $profile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($email);

        if (
            !isset($profile)
            || $profile->isAdmin()
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
            $pass = \XLite\Core\Database::getRepo('XLite\Model\Profile')->generatePassword();

            $profile->setPassword(\XLite\Core\Auth::encryptPassword($pass));
            $profile->setForceChangePassword(true);
            $profile->setPasswordResetKey('');
            $profile->setPasswordResetKeyDate(0);

            $result = $profile->update();

            if ($result) {
                $successfullyLogged = \XLite\Core\Auth::getInstance()->loginProfile($profile);

                if ($successfullyLogged) {
                    $profileCart = $this->getCart();

                    // We merge the logged in cart into the session cart
                    $profileCart->login($profile);
                    \XLite\Core\Database::getEM()->flush();

                    if ($profileCart->isPersistent()) {
                        $this->updateCart();
                        \XLite\Core\Event::getInstance()->exclude('updateCart');
                    }
                }
            }
        }

        return $result;
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
