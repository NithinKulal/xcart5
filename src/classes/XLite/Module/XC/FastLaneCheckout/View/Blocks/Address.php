<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FastLaneCheckout\View\Blocks;

use \XLite\Module\XC\FastLaneCheckout;

/**
 * Checkout Address form
 */
abstract class Address extends \XLite\View\Checkout\AAddressBlock
{
    /**
     * Get address type
     *
     * @return string
     */
    abstract protected function getAddressType();

    /**
     * Check - password field is visible or not
     *
     * @return boolean
     */
    protected function isPasswordVisible()
    {
        return false;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = array();

        $list[] = array(
            'file'  => FastLaneCheckout\Main::getSkinDir() . 'blocks/address/style.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        );

        return $list;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = array();

        $list[] = FastLaneCheckout\Main::getSkinDir() . 'blocks/address/address.js';

        return $list;
    }

    /**
     * @return void
     */
    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/address/template.twig';
    }

    /**
     * Get an array of address fields
     *
     * @return array
     */
    protected function getAddressFields()
    {
        $fields = \XLite::getController()->getAddressFields();
        $result = array();

        $omittedNames = array();

        foreach ($fields as $fieldName => $field) {
            if (!in_array($fieldName, $omittedNames)) {
                $value = $this->getFieldValue($fieldName, true);

                if (!$value) {
                    continue;
                }

                $result[$fieldName] = array(
                    'label' => $field['label'],
                    'value' => $value,
                    'attributes' => $this->getFieldAttributes($fieldName, $field),
                );
            }
        }

        if ($this->isEmailVisible()) {
            $result['email'] = array(
                'label' => 'Email',
                'value' => $this->getFieldValue($fieldName, true),
                'attributes' => $this->getFieldAttributes($fieldName, $field),
            );
        }

        return $result;
    }

    /**
     * Add CSS classes to the list of attributes
     *
     * @param string $fieldName Field service name
     * @param array  $fieldData Array of field properties (see getAddressFields() for the details)
     *
     * @return array
     */
    public function getFieldAttributes($fieldName, array $fieldData)
    {
        $classes = array('field-' . $fieldName);

        $attrs = empty($fieldData[\XLite\View\FormField\AFormField::PARAM_ATTRIBUTES])
            ? array()
            : $fieldData[\XLite\View\FormField\AFormField::PARAM_ATTRIBUTES];

        if (!isset($attrs['class'])) {
            $attrs['class'] = '';
        }

        $attrs['class'] = trim($attrs['class'] . ' ' . implode(' ', $classes));

        // Vue.js attributes

        return $attrs;
    }

    public function buildCountryNamesObject()
    {
        $countries = \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllEnabled();
        $dto = array_map(function ($item) {
            return [
                "key" => $item->getCode(),
                "name" => $item->getCountry()
            ];
        }, $countries);
        return json_encode($dto);
    }

    public function buildStateNamesObject()
    {
        $states = \XLite\Core\Database::getRepo('XLite\Model\State')->findAllStates();
        $dto = array_map(function ($item) {
            return [
                "key" => $item->getStateId(),
                "name" => $item->getState()
            ];
        }, $states);
        return json_encode($dto);
    }
}
