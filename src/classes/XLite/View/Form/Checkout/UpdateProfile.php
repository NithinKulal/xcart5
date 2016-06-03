<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Checkout;

/**
 * Checkout update profile form
 */
class UpdateProfile extends \XLite\View\Form\Checkout\ACheckout
{
    /**
     * Get default form action
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'update_profile';
    }

    /**
     * Required form parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list['returnURL'] = $this->buildURL('checkout', 'update_profile');

        return $list;
    }

    /**
     * Get validator
     *
     * @return \XLite\Core\Validator\HashArray
     */
    protected function getValidator()
    {
        $validator = parent::getValidator();

        $validator->addPair(
            'email',
            new \XLite\Core\Validator\String\Email,
            \XLite\Core\Validator\Pair\APair::SOFT
        );
        $validator->addPair(
            'password',
            new \XLite\Core\Validator\TypeString,
            \XLite\Core\Validator\Pair\APair::SOFT
        );
        $validator->addPair(
            'create_profile',
            new \XLite\Core\Validator\String\Switcher(),
            \XLite\Core\Validator\Pair\APair::SOFT
        );
        $validator->addPair(
            'guest_agree',
            new \XLite\Core\Validator\String\Switcher(),
            \XLite\Core\Validator\Pair\APair::SOFT
        );
        $validator->addPair(
            'same_address',
            new \XLite\Core\Validator\String\Switcher(),
            \XLite\Core\Validator\Pair\APair::SOFT
        );

        $onlyCalculate = (bool)\XLite\Core\Request::getInstance()->only_calculate;
        $mode = $onlyCalculate
            ? \XLite\Core\Validator\Pair\APair::SOFT
            : \XLite\Core\Validator\Pair\APair::STRICT;
        $nonEmpty = !$onlyCalculate;

        // Shipping address
        $shippingAddress = $validator->addPair(
            'shippingAddress',
            new \XLite\Core\Validator\HashArray,
            \XLite\Core\Validator\Pair\APair::SOFT
        );

        $addressFields = \XLite::getController()->getAddressFields();

        $isCountryStateAdded = false;

        foreach ($addressFields as $fieldName => $fieldData) {

            if (in_array($fieldName, array('country_code', 'state_id'))) {

                if (!$isCountryStateAdded) {
                    $shippingAddress->addPair(new \XLite\Core\Validator\Pair\CountryState());
                    $isCountryStateAdded = true;
                }

            } else {
                $mode = ($onlyCalculate || !$fieldData[\XLite\View\Model\Address\Address::SCHEMA_REQUIRED])
                    ? \XLite\Core\Validator\Pair\APair::SOFT
                    : \XLite\Core\Validator\Pair\APair::STRICT;
                $shippingAddress->addPair(
                    $fieldName,
                    new \XLite\Core\Validator\TypeString($nonEmpty && $fieldData[\XLite\View\Model\Address\Address::SCHEMA_REQUIRED]),
                    $mode
                );
            }
        }

        $shippingAddress->addPair(
            'save_in_book',
            new \XLite\Core\Validator\String\Switcher(),
            \XLite\Core\Validator\Pair\APair::SOFT
        );

        // Billing address
        if (!\XLite\Core\Request::getInstance()->same_address) {
            $billingAddress = $validator->addPair(
                'billingAddress',
                new \XLite\Core\Validator\HashArray,
                \XLite\Core\Validator\Pair\APair::SOFT
            );

            $isCountryStateAdded = false;

            foreach ($addressFields as $fieldName => $fieldData) {

                if (in_array($fieldName, array('country_code', 'state_id'))) {

                    if (!$isCountryStateAdded) {
                        $billingAddress->addPair(new \XLite\Core\Validator\Pair\CountryState());
                        $isCountryStateAdded = true;
                    }

                } else {
                    $billingAddress->addPair(
                        $fieldName,
                        new \XLite\Core\Validator\TypeString($nonEmpty && $fieldData[\XLite\View\Model\Address\Address::SCHEMA_REQUIRED]),
                        $mode
                    );
                }
            }

            $billingAddress->addPair(
                'save_in_book',
                new \XLite\Core\Validator\String\Switcher(),
                \XLite\Core\Validator\Pair\APair::SOFT
            );
        }

        return $validator;
    }
}
