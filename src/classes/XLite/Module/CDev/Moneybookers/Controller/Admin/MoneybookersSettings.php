<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Moneybookers\Controller\Admin;

/**
 * Skrill settings controller
 */
class MoneybookersSettings extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Skrill settings');
    }

    /**
     * Validate email
     *
     * @return void
     */
    protected function doActionCheckEmail()
    {
        $email = \XLite\Core\Request::getInstance()->email;
        $id = intval(\XLite\Core\Request::getInstance()->id);
        $sword = \XLite\Module\CDev\Moneybookers\Model\Payment\Processor\Moneybookers::getPlatformSecretWord();
        $sword = md5($sword);

        $platformId = \XLite\Module\CDev\Moneybookers\Model\Payment\Processor\Moneybookers::getPlatformCustomerID();
        $request = new \XLite\Core\HTTP\Request(
            'https://www.skrill.com/app/email_check.pl'
            . '?email=' . urlencode($email)
            . '&cust_id=' . $platformId
            . '&password=' . $sword
        );
        $response = $request->sendRequest();

        $codes = explode(',', $response->body, 2);

        if (
            200 == $response->code
            && 'OK' == $codes[0]
            && isset($codes[1])
            && is_numeric($codes[1])
            && 0 < intval($codes[1])
        ) {
            // Save settings
            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
                array(
                    'category' => 'CDev\Moneybookers',
                    'name'     => 'email',
                    'value'    => $email,
                )
            );
            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
                array(
                    'category' => 'CDev\Moneybookers',
                    'name'     => 'id',
                    'value'    => $codes[1],
                )
            );

            \XLite\Core\TopMessage::getInstance()->add('E-mail address is valid');

        } else {
            \XLite\Core\TopMessage::getInstance()->add(
                'E-mail address is not valid',
                array(),
                null,
                \XLite\Core\TopMessage::ERROR
            );
        }
    }

    /**
     * Activate Moneybookers Quick Checkout
     *
     * @return void
     */
    protected function doActionActivate()
    {
        if (
            \XLite\Core\Config::getInstance()->CDev->Moneybookers->email != \XLite\Core\Request::getInstance()->email
            || !\XLite\Core\Config::getInstance()->CDev->Moneybookers->email
            || \XLite\Core\Config::getInstance()->CDev->Moneybookers->id != \XLite\Core\Request::getInstance()->id
            || !\XLite\Core\Config::getInstance()->CDev->Moneybookers->id
        ) {
            $this->doActionCheckEmail();
        }

        if (
            \XLite\Core\Config::getInstance()->CDev->Moneybookers->email
            && \XLite\Core\Config::getInstance()->CDev->Moneybookers->id
        ) {
            \XLite\Core\Mailer::sendMoneybookersActivation();
            \XLite\Core\TopMessage::getInstance()->add(
                'You have sent a request for activation on the X.',
                array('date' => date('m.d.Y'))
            );
        }
    }

    /**
     * Validate secret word
     *
     * @return void
     */
    protected function doActionValidateSecretWord()
    {
        $secretWord = trim(\XLite\Core\Request::getInstance()->secret_word);
        $secret = md5(
            md5($secretWord)
            . md5(\XLite\Module\CDev\Moneybookers\Model\Payment\Processor\Moneybookers::getPlatformSecretWord())
        );

        $platformId = \XLite\Module\CDev\Moneybookers\Model\Payment\Processor\Moneybookers::getPlatformCustomerID();
        $request = new \XLite\Core\HTTP\Request(
            'https://www.skrill.com/app/secret_word_check.pl'
            . '?secret=' . $secret
            . '&email=' . urlencode(\XLite\Core\Config::getInstance()->CDev->Moneybookers->email)
            . '&cust_id=' . $platformId
        );
        $response = $request->sendRequest();

        if (200 == $response->code && 'OK' == $response->body) {
            \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
                array(
                    'category' => 'CDev\Moneybookers',
                    'name'     => 'secret_word',
                    'value'    => $secretWord,
                )
            );

            \XLite\Core\TopMessage::getInstance()->add('Secret word is valid');

        } elseif ('VELOCITY_CHECK_EXCEEDED' == $response->body) {
            \XLite\Core\TopMessage::getInstance()->add(
                'Maximum number of checks for a particular user has been reached'
                . ' (currently set to 3 per user per hour)',
                array(),
                null,
                \XLite\Core\TopMessage::ERROR
            );

        } elseif ('REQUESTER_NOT_AUTHORISED' == $response->body) {
            \XLite\Core\TopMessage::getInstance()->add(
                'Requestor\'s account not authorised to run the secret word check',
                array(),
                null,
                \XLite\Core\TopMessage::ERROR
            );

        } else {
            \XLite\Core\TopMessage::getInstance()->add(
                'Secret word is not valid',
                array(),
                null,
                \XLite\Core\TopMessage::ERROR
            );
        }
    }

    /**
     * Set order id prefix
     *
     * @return void
     */
    protected function doActionSetOrderPrefix()
    {
        \XLite\Core\Database::getRepo('\XLite\Model\Config')->createOption(
            array(
                'category' => 'CDev\Moneybookers',
                'name'     => 'prefix',
                'value'    => trim(\XLite\Core\Request::getInstance()->prefix),
            )
        );
    }
}
