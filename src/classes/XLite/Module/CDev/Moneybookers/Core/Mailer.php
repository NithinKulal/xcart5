<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Moneybookers\Core;

/**
 * Mailer
 */
class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    const TYPE_MONEYBOOKERS_ACTIVATION = 'siteAdmin';
    
    /**
     * Send Moneybookers activation message
     *
     * @return void
     */
    public static function sendMoneybookersActivation()
    {
        // Register variables
        static::register(
            'platform_name',
            \XLite\Module\CDev\Moneybookers\Model\Payment\Processor\Moneybookers::getPlatformName()
        );
        $address = \XLite\Core\Auth::getInstance()->getProfile()->getBillingAddress();
        if ($address) {
            static::register('first_name', $address->getFirstName());
            static::register('last_name', $address->getLastName());

        } else {
            static::register('first_name', '');
            static::register('last_name', '');

        }
        static::register('email', \XLite\Core\Config::getInstance()->CDev->Moneybookers->email);
        static::register('id', \XLite\Core\Config::getInstance()->CDev->Moneybookers->id);
        static::register('url', \XLite::getInstance()->getShopURL());
        static::register('language', \XLite\Core\Session::getInstance()->getLanguage()->getCode());

        static::getMailer()->setSubjectTemplate('modules/CDev/Moneybookers/activation/subject.twig');
        static::getMailer()->setLayoutTemplate('modules/CDev/Moneybookers/activation/body.twig');

        // Compose and send email
        static::compose(
            static::TYPE_MONEYBOOKERS_ACTIVATION,
            static::getSiteAdministratorMail(),
            'ecommerce@skrill.com',
            'modules/CDev/Moneybookers/activation'
        );
    }

}

