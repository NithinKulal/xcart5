<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Checkout;

/**
 * Address block info
 */
abstract class AAddressBlock extends \XLite\View\Checkout\AAddressBlock implements \XLite\Base\IDecorator
{
    /**
     * @inheritDoc
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'checkout/fields-height-controller.js'
            ]
        );
    }

    /**
     * Get an array of address fields
     *
     * @return array
     */
    protected function getAddressFields()
    {
        $result = array();

        if ($this->isEmailVisible()) {
            $result['email'] = array(
                \XLite\View\Model\Address\Address::SCHEMA_CLASS            => 'XLite\View\FormField\Input\Text\CheckoutEmail',
                \XLite\View\Model\Address\Address::SCHEMA_LABEL            => 'Email',
                \XLite\View\Model\Address\Address::SCHEMA_REQUIRED         => true,
                \XLite\View\Model\Address\Address::SCHEMA_MODEL_ATTRIBUTES => array(
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
                ),
                \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS      => 'address-email',
                \XLite\View\FormField\AFormField::PARAM_COMMENT            => static::t('Your order details will be sent to your e-mail address'),
                \XLite\View\FormField\AFormField::PARAM_ATTRIBUTES         => array(
                    'class' => 'progress-mark-owner',
                ),
                'additionalClass'                                          => $this->getEmailClassName(),
            );
        }

        if ($this->isPasswordVisible()) {
            $result['password'] = array(
                \XLite\View\Model\Address\Address::SCHEMA_CLASS            => 'XLite\View\FormField\Input\PasswordVisible',
                \XLite\View\Model\Address\Address::SCHEMA_LABEL            => 'Password',
                \XLite\View\Model\Address\Address::SCHEMA_REQUIRED         => true,
                \XLite\View\Model\Address\Address::SCHEMA_MODEL_ATTRIBUTES => array(
                    \XLite\View\FormField\Input\Base\StringInput::PARAM_MAX_LENGTH => 'length',
                ),
                \XLite\View\FormField\AFormField::PARAM_WRAPPER_CLASS      => 'password',
                'additionalClass'                                          => $this->getPasswordClassName(),
            );
        }

        return  array_merge($result, parent::getAddressFields());
    }

    /**
     * Get field placeholder
     *
     * @param string $name File short name
     *
     * @return string
     */
    protected function getFieldPlaceholder($name)
    {
        switch ($name) {
            case 'firstname':
                $result = static::t('Joe');
                break;

            case 'lastname':
                $result = static::t('Public');
                break;

            case 'street':
                $result = static::t('1000 Main Street');
                break;

            case 'city':
                $result = static::t('Anytown');
                break;

            case 'custom_state':
                $result = static::t('Anyland');
                break;

            case 'zipcode':
                $result = static::t('90001');
                break;

            case 'phone':
                $result = static::t('+15550000000');
                break;

            case 'email':
                $result = static::t('email@example.com');
                break;

            default:
                $result = '';
        }

        return $result;
    }
}
