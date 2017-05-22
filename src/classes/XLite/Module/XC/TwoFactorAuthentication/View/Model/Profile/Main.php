<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\TwoFactorAuthentication\View\Model\Profile;

/**
 * \XLite\View\Model\Profile\Main
 */
class Main extends \XLite\View\Model\Profile\Main implements \XLite\Base\IDecorator
{
    /**
     * Schema for phone number and phone country code
     *
     * @var array
     */
    protected $auth_phone = array(
        'auth_phone_code' => array(
            self::SCHEMA_CLASS       => '\XLite\Module\XC\TwoFactorAuthentication\View\FormField\Input\Text\PhoneCode',
            self::SCHEMA_LABEL       => 'Country phone code',
            self::SCHEMA_REQUIRED    => false,
            self::SCHEMA_PLACEHOLDER => '+1',
        ),
        'auth_phone_number' => array(
            self::SCHEMA_CLASS       => '\XLite\View\FormField\Input\Text\Phone',
            self::SCHEMA_LABEL       => 'Phone number',
            self::SCHEMA_REQUIRED    => false,
            self::SCHEMA_PLACEHOLDER => '9178007060',
            self::SCHEMA_HELP        => 'Type your phone number here to receive a SMS code for two-factor-authentication'
        )
    );

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        $schema = array_merge($this->mainSchema, $this->auth_phone);

        // Modify the main schema
        $this->mainSchema = $schema;

        return parent::getFormFieldsForSectionMain();
    }
}