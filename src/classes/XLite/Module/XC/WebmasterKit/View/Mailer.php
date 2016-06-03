<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\View;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\View\Mailer implements \XLite\Base\IDecorator
{
    /**
     * Send message
     *
     * @return boolean
     */
    public function send()
    {
        if (\XLite\Core\Config::getInstance()->XC->WebmasterKit->logMail) {
            \XLite\Logger::getInstance()->logCustom(
                'mail-messages',
                'From: ' . $this->mail->From . PHP_EOL
                . 'To: ' . $this->get('to') . PHP_EOL
                . 'Subject: ' . $this->mail->Subject . PHP_EOL
                . $this->mail->Body . PHP_EOL
                . PHP_EOL
            );
        }

        return parent::send();
    }
}

