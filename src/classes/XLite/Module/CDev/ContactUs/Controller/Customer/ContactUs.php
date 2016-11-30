<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ContactUs\Controller\Customer;

use XLite\Core\Config;
use XLite\Core\Mailer;
use XLite\Core\Session;
use XLite\Core\TopMessage;

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
    protected $requiredFields = [
        'name' => 'Your name',
        'email' => 'Your e-mail',
        'subject' => 'Subject',
        'message' => 'Message'
    ];

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess() && Config::getInstance()->CDev->ContactUs->enable_form;
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return Config::getInstance()->Security->customer_security;
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
        $data = Session::getInstance()->contact_us;

        $value = $data && isset($data[$field]) ? $data[$field] : '';

        if (!$value && in_array($field, ['name', 'email'])) {
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
        $isValid = true;

        foreach ($this->requiredFields as $key => $name) {
            if (
                !isset($data[$key])
                || empty($data[$key])
            ) {
                $isValid = false;
                TopMessage::addError(
                    static::t('The X field is empty', ['name' => $name])
                );
            }
        }

        if ($isValid && false === filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $isValid = false;
            TopMessage::addError('The value of the X field has an incorrect format', ['name' => $this->requiredFields['email']]);
        }

        $reCaptcha = \XLite\Module\CDev\ContactUs\Core\ReCaptcha::getInstance();

        if ($isValid && $reCaptcha->isConfigured()) {
            $response = $reCaptcha->verify(isset($data['g-recaptcha-response']) ? $data['g-recaptcha-response'] : '');

            $isValid = $response && $response->isSuccess();

            if (!$isValid) {
                TopMessage::addError('Please enter the correct captcha');
            }
        }

        if ($isValid) {
            $errorMessage = Mailer::sendContactUsMessage(
                $data,
                Config::getInstance()->CDev->ContactUs->email
                    ?: Config::getInstance()->Company->support_department
            );

            if ($errorMessage) {
                TopMessage::addError($errorMessage);

            } else {
                unset($data['message']);
                unset($data['subject']);
                TopMessage::addInfo('Message has been sent');
            }
        }

        Session::getInstance()->contact_us = $data;
    }
}
