<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input;

/**
 * Service name field for address field
 */
class AddressFieldsServiceName extends \XLite\View\FormField\Inline\Input\Text
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Text\AddressFieldsServiceName';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-address-fields-service-name';
    }

    /**
     * Validate address field service name
     *
     * @param array $field Field info
     *
     * @return array
     */
    protected function validateServiceName(array $field)
    {
        $result = array(true, null);

        try {
            $addressField = $this->getEntity();

            if ($addressField) {
                $validator = new \XLite\Core\Validator\UniqueField(
                    get_class($addressField),
                    'serviceName',
                    $addressField->getServiceName()
                );
                $validator->validate($field['widget']->getValue());
            }
        } catch (\Exception $e) {
            $result = array(
                false,
                $e->getMessage()
            );
        }

        return $result;
    }
}

