<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\Controller\Customer;

/**
 * Contact us controller
 */
class ContactUs extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Fields
     *
     * @var   array
     */
    protected $requiredFields = array(
        'name'    => 'Your name',
        'email'   => 'Your e-mail',
        'subject' => 'Subject',
        'message' => 'Message'
    );

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && \XLite\Core\Config::getInstance()->CDev->ContactUs->enable_form;
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Contact us');
    }

    /**
     * Return value of data
     *
     * @param string $field Field
     *
     * @return string
     */
    public function getValue($field)
    {
        $data = \XLite\Core\Session::getInstance()->contact_us;

        $value = $data && isset($data[$field]) ? $data[$field] : '';

        if (
            !$value
            && in_array($field, array('name', 'email'))
        ) {
            $auth = \XLite\Core\Auth::getInstance();
            if ($auth->isLogged()) {
                if ('email' == $field) {
                    $value = $auth->getProfile()->getLogin();

                } elseif (0 < $auth->getProfile()->getAddresses()->count()) {
                    $value = $auth->getProfile()->getAddresses()->first()->getName();
                }
            }
        }

        return $value;
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
     * Send message
     *
     * @return void
     */
    protected function doActionSend()
    {
        $data = \XLite\Core\Request::getInstance()->getData();
        $config = \XLite\Core\Config::getInstance()->CDev->ContactUs;
        $isValid = true;

        foreach ($this->requiredFields as $key => $name) {
            if (
                !isset($data[$key])
                || empty($data[$key])
            ) {
                $isValid = false;
                \XLite\Core\TopMessage::addError(
                    static::t('The X field is empty', array('name' => $name))
                );
            }
        }

        if (
            $isValid
            && false === filter_var($data['email'], FILTER_VALIDATE_EMAIL)
        ) {
            $isValid = false;
            \XLite\Core\TopMessage::addError(
                \XLite\Core\Translation::lbl(
                    'The value of the X field has an incorrect format',
                    array('name' => $this->requiredFields['email'])
                )
            );
        }

        if (
            $isValid
            && $config->recaptcha_private_key
            && $config->recaptcha_public_key
        ) {
            require_once LC_DIR_MODULES . '/CDev/ContactUs/recaptcha/recaptchalib.php';

            $resp = recaptcha_check_answer(
                $config->recaptcha_private_key,
                $_SERVER['REMOTE_ADDR'],
                $data['recaptcha_challenge_field'],
                $data['recaptcha_response_field']
            );

            $isValid = $resp->is_valid;

            if (!$isValid) {
                \XLite\Core\TopMessage::addError('Please enter the correct captcha');
            }
        }

        if ($isValid) {
            $errorMessage = \XLite\Core\Mailer::sendContactUsMessage(
                $data,
                \XLite\Core\Config::getInstance()->CDev->ContactUs->email
                    ?: \XLite\Core\Config::getInstance()->Company->support_department
            );

            if ($errorMessage) {
                \XLite\Core\TopMessage::addError($errorMessage);

            } else {
                unset($data['message']);
                unset($data['subject']);
                \XLite\Core\TopMessage::addInfo('Message has been sent');
            }
        }

        \XLite\Core\Session::getInstance()->contact_us = $data;
    }
}
