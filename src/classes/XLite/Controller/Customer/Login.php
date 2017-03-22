<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer;

/**
 * Login page controller
 */
class Login extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Index in request array; the secret token used for authorization
     */
    const SECURE_TOKEN = 'secureToken';

    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('target', 'mode');

    /**
     * Profile found by login (without password check)
     *
     * @var \XLite\Model\Profile
     */
    protected $foundProfile;

    /**
     * Profile
     *
     * @var \XLite\Model\Profile|integer
     */
    protected $profile;

    /**
     * Set if the form id is needed to make an actions
     * Form class uses this method to check if the form id should be added
     *
     * @return boolean
     */
    public static function needFormId()
    {
        return true;
    }

    /**
     * Initialization
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        if (empty(\XLite\Core\Request::getInstance()->login)) {
            \XLite\Core\Request::getInstance()->login = \XLite\Core\Auth::getInstance()->remindLogin();
        }
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Sign in');
    }

    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        if (
            \XLite\Core\Auth::getInstance()->isLogged()
            && 'logoff' !== \XLite\Core\Request::getInstance()->{static::PARAM_ACTION}
        ) {
            $this->setHardRedirect(true);
            $this->setReturnURL($this->buildURL());
        }

        parent::handleRequest();
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return 'logoff' !== \XLite\Core\Request::getInstance()->action
            ? \XLite\Core\Config::getInstance()->Security->customer_security
            : parent::isSecure();
    }

    /**
     * Perform some actions after the "login" action
     *
     * @return void
     */
    public function redirectFromLogin()
    {
        $url = $this->getRedirectFromLoginURL();

        if (isset($url)) {
            \XLite\Core\CMSConnector::isCMSStarted()
                ? \XLite\Core\Operator::redirect($url, true)
                : $this->setReturnURL($url);
        }
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Return URL to redirect from login
     *
     * @return string
     */
    protected function getRedirectFromLoginURL()
    {
        return null;
    }

    /**
     * Log in using the login and password from request
     *
     * @return \XLite\Model\Profile
     */
    protected function performLogin()
    {
        $data = \XLite\Core\Request::getInstance()->getData();
        $token = empty($data[self::SECURE_TOKEN]) ? null : $data[self::SECURE_TOKEN];

        $this->foundProfile = \XLite\Core\Database::getRepo('XLite\Model\Profile')->findByLogin($data['login']);

        if ($this->isLoginDisabled($this->foundProfile)) {
            // Log in is impossible: return 'ACCESS DENIED' message
            $result = \XLite\Core\Auth::RESULT_ACCESS_DENIED;
            $this->foundProfile = null;

        } elseif ($this->foundProfile && $this->foundProfile->isAdmin()) {
            $result = \XLite\Core\Auth::getInstance()->loginAdministrator($data['login'], $data['password']);

        } else {
            // Try to log in
            $result = \XLite\Core\Auth::getInstance()->login($data['login'], $data['password'], $token);
        }

        return $result;
    }

    /**
     * Return true if $profile is an administrator's profile
     * Administrator can not to log in via customer zone
     *
     * @param \XLite\Model\Profile $profile User profile
     *
     * @return boolean
     */
    protected function isLoginDisabled($profile)
    {
        return $profile && $profile->isAdmin();
    }

    /**
     * Login
     *
     * @return void
     */
    protected function doActionLogin()
    {
        if ($this->isLoginPermitted()) {
            if ($this->isAlternativeLoginUsed()) {
                $this->alternativeLoginBody();
            } else {
                $this->loginBody();
            }
        }
    }

    /**
     * Check if login permitted
     *
     * @return boolean
     */
    protected function isLoginPermitted()
    {
        return true;
    }

    /**
     * Check if alternative login uses
     *
     * @return boolean
     */
    protected function isAlternativeLoginUsed()
    {
        return false;
    }

    /**
     * Alternative login body
     *
     * @return void
     */
    protected function alternativeLoginBody()
    {
    }

    /**
     * Login body
     *
     * @return void
     */
    protected function loginBody()
    {
        $this->profile = $this->performLogin();

        if (!($this->profile instanceof \XLite\Model\Profile)) {

            $this->addLoginFailedMessage(\XLite\Core\Auth::RESULT_ACCESS_DENIED);

            \XLite\Logger::getInstance()
                ->log(sprintf('Log in action is failed (%s)', \XLite\Core\Request::getInstance()->login), LOG_WARNING);

            if ($this->isNeedFailureRedirect()) {
                // Redirect to admin login page is needed if founcProfile is an administrator profile
                $url = \XLite\Core\URLManager::getShopURL(
                    \XLite\Core\Converter::buildURL('login', '', array(), \XLite::getAdminScript())
                );
                $this->setReturnURL($url);
                $this->setHardRedirect(true);

            } else {
                $this->set('valid', false);
            }

        } else {
            if (\XLite\Core\Request::getInstance()->returnURL) {
                $url = preg_replace(
                    '/' . preg_quote(\XLite\Core\Session::getInstance()->getName()) . '=([^&]+)/',
                    '',
                    \XLite\Core\Request::getInstance()->returnURL
                );
                $this->setReturnURL($url);
            }

            $profileCart = $this->getCart();

            $this->setReturnURL($this->getReferrerURL());

            if (!$this->getReturnURL() && !$profileCart->isEmpty()) {
                $this->setReturnURL(
                    \XLite\Core\Converter::buildURL('cart')
                );
            } elseif ($this->getReturnURL() === \XLite\Core\Converter::buildURL('login')) {
                $this->setReturnURL(
                    \XLite\Core\Converter::buildURL('main')
                );
            }

            $this->setHardRedirect();

            // We merge the logged in cart into the session cart
            $profileCart->login($this->profile);
            \XLite\Core\Database::getEM()->flush();

            if ($profileCart->isPersistent()) {
                $this->updateCart();
                \XLite\Core\Event::getInstance()->exclude('updateCart');
            }
        }
    }

    /**
     * Return true if profile was found by login but password does not match and founc profile is an administrator's profile
     *
     * @return boolean
     */
    protected function isNeedFailureRedirect()
    {
        return $this->foundProfile && $this->foundProfile->isAdmin();
    }

    /**
     * Log out
     *
     * @return void
     */
    protected function doActionLogoff()
    {
        if (\XLite\Core\Auth::getInstance()->isOperatingAsUserMode()) {
            $this->setReturnURL(
                \XLite\Core\Converter::buildURL(
                    'profile',
                    '',
                    array(
                        'profile_id' => \XLite\Core\Auth::getInstance()->getOperatingAs()
                    ),
                    \XLite::getAdminScript()
                )
            );

            \XLite\Core\Auth::getInstance()->finishOperatingAs();
            \XLite\Core\TopMessage::addInfo('Finished operating as user');
        } else {
            \XLite\Core\Auth::getInstance()->logoff();

            \Includes\Utils\Session::clearAdminCookie();

            $this->setReturnURL(\XLite\Core\Converter::buildURL());

            $this->getCart()->logoff();
            $this->updateCart();

            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Perform some actions before redirect
     *
     * @return void
     */
    protected function actionPostprocessLogin()
    {
        $this->redirectFromLogin();
    }

    /**
     * Add top message if log in is failed
     *
     * @param mixed $result Result of log in procedure
     *
     * @return void
     */
    protected function addLoginFailedMessage($result)
    {
        if (in_array($result, array(\XLite\Core\Auth::RESULT_ACCESS_DENIED, \XLite\Core\Auth::RESULT_PASSWORD_NOT_EQUAL))) {
            \XLite\Core\TopMessage::addError('Invalid login or password');
            \XLite\Core\Event::invalidForm('login-form', static::t('Invalid login or password'));
        }
    }
}
