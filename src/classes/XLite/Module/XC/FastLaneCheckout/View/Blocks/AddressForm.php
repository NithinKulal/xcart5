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
abstract class AddressForm extends \XLite\View\Checkout\AAddressBlock
{
    /**
     * Returns block class name
     *
     * @return boolean
     */
    abstract public function getClassName();

    /**
     * Get JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = array();

        $list[] = array(
            'file'  => FastLaneCheckout\Main::getSkinDir() . 'blocks/address_form/style.less',
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

        $list[] = FastLaneCheckout\Main::getSkinDir() . 'blocks/address_form/address_form.js';

        return $list;
    }

    public function getListName($field = null)
    {
        $name = 'checkout_fastlane.blocks.address.' . $this->getClassName();

        if ($field) {
            $name .= '.' . $field;
        }

        return $name;
    }

    /**
     * @return void
     */
    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/address_form/template.twig';
    }

    /**
     * Check - form is visible or not
     *
     * @return boolean
     */
    protected function isFormVisible()
    {
        return true;
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

        if (!empty($fieldData[\XLite\View\Model\Address\Address::SCHEMA_REQUIRED])) {
            $classes[] = 'field-required';
        }

        $attrs = empty($fieldData[\XLite\View\FormField\AFormField::PARAM_ATTRIBUTES])
            ? array()
            : $fieldData[\XLite\View\FormField\AFormField::PARAM_ATTRIBUTES];

        if (!isset($attrs['class'])) {
            $attrs['class'] = '';
        }

        $attrs['class'] = trim($attrs['class'] . ' ' . implode(' ', $classes));

        // Vue.js attributes
        $attrs['v-model'] = "value";
        $attrs['v-el:input'] = "v-el:input";
        $attrs['debounce'] = '500';
        $attrs['lazy'] = 'lazy';

        return $attrs;
    }

    /**
     * Get field css classes
     *
     * @param string $fieldName Field service name
     *
     * @return array
     */
    protected function getFieldClasses($fieldName)
    {
        return array(
            'field-' . $fieldName,
        );
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
                $result = static::t('Firstname');
                break;

            case 'lastname':
                $result = static::t('Lastname');
                break;

            case 'street':
                $result = static::t('Street address');
                break;

            case 'city':
                $result = static::t('City');
                break;

            case 'custom_state':
                $result = static::t('State');
                break;

            case 'zipcode':
                $result = static::t('Zipcode');
                break;

            case 'phone':
                $result = static::t('Phone number');
                break;

            case 'email':
                $result = static::t('E-mail');
                break;

            default:
                $result = '';
        }

        return $result;
    }
}
