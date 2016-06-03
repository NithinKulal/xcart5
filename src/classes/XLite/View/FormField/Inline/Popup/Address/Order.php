<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Popup\Address;

/**
 * Order's address
 */
class Order extends \XLite\View\FormField\Inline\Popup\Address
{
    const SAME_AS_BILLING = 'same_as_billing';

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'address/order/style.css';

        return $list;
    }

    /**
     * Get popup widget
     *
     * @return string
     */
    protected function getPopupWidget()
    {
        return '\XLite\View\Address\OrderModify';
    }

    /**
     * Get popup target
     *
     * @return string
     */
    protected function getPopupTarget()
    {
        return 'order';
    }

    /**
     * Get popup parameters
     *
     * @return array
     */
    protected function getPopupParameters()
    {
        $list = parent::getPopupParameters();

        $order = $this->getEntity()->getOrder() ?: $this->getOrder();

        $list['order_id'] = $order->getOrderId();

        return $list;
    }

    /**
     * Define fields
     *
     * @return array
     */
    protected function defineFields()
    {
        $fields = parent::defineFields();

        $fields[$this->getAddressIdFieldName()] = array(
            static::FIELD_NAME  => $this->getAddressIdFieldName(),
            static::FIELD_CLASS => 'XLite\View\FormField\Input\Hidden',
        );

        if ('shippingAddress' == $this->getParam(static::PARAM_FIELD_NAME)) {
            $fields[static::SAME_AS_BILLING] = array(
                static::FIELD_NAME  => static::SAME_AS_BILLING,
                static::FIELD_CLASS => 'XLite\View\FormField\Input\Hidden',
            );
        }

        return $fields;
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        if ($this->getAddressIdFieldName() == $field[static::FIELD_NAME]) {
            $addressMethod = 'get' . ucfirst($this->getParam(static::PARAM_FIELD_NAME));
            $result = $this->getEntity()->$addressMethod()
                ? $this->getEntity()->$addressMethod()->getAddressId()
                : null;

        } elseif (static::SAME_AS_BILLING == $field[static::FIELD_NAME]) {
            $result = $this->isSameAsBilling() ? '1' : '0';

        } else {
            $result = parent::getFieldEntityValue($field);
        }

        return $result;
    }

    protected function getOrderHistoryExcludedFields()
    {
        return array(
            static::SAME_AS_BILLING,
            $this->getAddressIdFieldName()
        );
    }

    /**
     * Save field value to entity
     *
     * @param array $field Field
     * @param mixed $value Value
     *
     * @return void
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        $excluded = $this->getOrderHistoryExcludedFields();

        if ($this->canSaveAddressFields() && $this->isWritableField($field)) {
            parent::saveFieldEntityValue($field, $value);

        } elseif (!in_array($field['field'][static::FIELD_NAME], $excluded)) {
            $address = $this->getAddressModel();

            if ($address) {
                $oldValue = $this->getOldPropertyValue($address, $field, $value);

                if ($value != $oldValue) {
                    $this->registerOrderChanges($address, $field, $oldValue, $value);
                }
            }
        }
    }

    /**
     * Check - can save address fields
     * 
     * @return boolean
     */
    protected function canSaveAddressFields()
    {
        $name1 = $this->getParam(static::PARAM_FIELD_NAME);

        $request = \XLite\Core\Request::getInstance();

        $result = true;
        if ($request->$name1 && is_array($request->$name1)) {
            $name2 = $this->getAddressIdFieldName();
            $data = $request->$name1;
            if (!empty($data[$name2])) {
                $result = false;
            }
        }

        if (
            $result
            && 'shippingAddress' == $this->getParam(static::PARAM_FIELD_NAME)
            && !empty($request->shippingAddress[static::SAME_AS_BILLING])
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check - field is writable or not
     * 
     * @param array $field Field
     *  
     * @return boolean
     * @since  ____since____
     */
    protected function isWritableField(array $field)
    {
        return !in_array(
            $field['field'][static::FIELD_NAME],
            array_merge(
                array($this->getAddressIdFieldName(), static::SAME_AS_BILLING),
                $this->getConditionalFields($field)
            )
        );
    }

    /**
     * Get conditional read-only fields
     * 
     * @param array $field Field
     * 
     * @return string
     */
    protected function getConditionalFields(array $field)
    {
        $fields = array();

        // Make custom_state readonly if country has states
        $countryWidget = $this->getFieldWidget($this->getCountryCodeFieldName());
        if ($countryWidget && $countryWidget->getValue()) {
            $countryCode = $countryWidget->getValue();
            $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($countryCode);

            if ($country && $country->hasStates()) {
                $fields[] = $this->getCustomStateFieldName();
            }
        }

        return $fields;
    }


    /**
     * Get country code field name 
     * 
     * @return string
     */
    protected function getCountryCodeFieldName()
    {
        return 'country_code';
    }

    /**
     * Get custom state field name 
     * 
     * @return string
     */
    protected function getCustomStateFieldName()
    {
        return 'custom_state';
    }

    /**
     * Get address ID field name 
     * 
     * @return string
     */
    protected function getAddressIdFieldName()
    {
        return 'id';
    }

    /**
     * Get view template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'form_field/inline/popup/address/order/view.twig';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        $class = parent::getContainerClass();

        $class .= ' inline-order-address';

        if ($this->isSameAsBilling()) {
            $class .= ' same-as-billing';
        }

        return trim($class);
    }

    /**
     * Check - addrss is shipping and equal billing
     * 
     * @return boolean
     */
    protected function isSameAsBilling()
    {
        return 'shippingAddress' == $this->getParam(static::PARAM_FIELD_NAME)
            && $this->getEntity()->isEqualAddress(true);
    }

}
