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
    const PARAM_DISPLAY_TYPE = 'display';

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
     * Returns display type 
     * 
     * @return string
     */
    protected function getDisplayType()
    {
        return $this->getParam(self::PARAM_DISPLAY_TYPE);
    }

    /**
     * @return string
     */
    protected function getDefaultDisplayType()
    {
        return \XLite\Core\Request::getInstance()->display ?: 'full';
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_DISPLAY_TYPE => new \XLite\Model\WidgetParam\TypeString('Display type', $this->getDefaultDisplayType()),
        );
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
     * @return void
     */
    protected function getEditAddressTitle()
    {
        return static::t('Edit address');
    }

    /**
     * Returns address id
     * 
     * @return integer
     */
    protected function getAddressId()
    {
        $address = $this->getAddressInfo();

        return $address ? $address->getAddressId() : 0;
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

        foreach ($fields as $fieldName => $field) {
            $value = $this->getFieldValue($fieldName, true);

            // // TODO: not sure if needed
            // if (!$value && $value !== '') {
            //     continue;
            // }

            $result[$fieldName] = array(
                'label' => $field['label'],
                'value' => $value,
                'attributes' => $this->getFieldAttributes($fieldName, $field),
            );
        }

        if ($this->isEmailVisible()) {
            $result['email'] = array(
                'label' => 'Email',
                'value' => $this->getFieldValue('email', true),
                'attributes' => $this->getFieldAttributes('email', []),
            );
        }

        return $result;
    }

    /**
     * @return string
     */
    public function defineFormSchema()
    {
        $schema = array();
        foreach ($this->getAddressFields() as $field => $data) {
            $schema[$field] = (string) $this->getFieldValue($field);
        }

        return $schema;
    }

    /**
     * @return string
     */
    public function serializeFormSchema()
    {
        return json_encode($this->defineFormSchema());
    }
}
