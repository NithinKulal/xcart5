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
class PopupAddressForm extends \XLite\View\Checkout\AAddressBlock
{
    const PARAM_ADDRESS_TYPE = 'type';

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
     * Check - email field is visible or not
     *
     * @return boolean
     */
    protected function isEmailVisible()
    {
        return false;
    }

    /**
     * Check - shipping and billing addrsses are same or not
     *
     * @return boolean
     */
    protected function isSameAddress()
    {
        return is_null(\XLite\Core\Session::getInstance()->same_address)
            ? $this->isSameAddressVisible() && (!$this->getCart()->getProfile() || $this->getCart()->getProfile()->isEqualAddress())
            : \XLite\Core\Session::getInstance()->same_address && $this->getCart()->isShippingSectionVisible();
    }

    /**
     * Check - same address box is visible or not
     *
     * @return boolean
     */
    protected function isSameAddressVisible()
    {
        return $this->getShippingModifier() && $this->getShippingModifier()->canApply();
    }

    /**
     * Get modifier
     *
     * @return \XLite\Model\Order\Modifier
     */
    protected function getShippingModifier()
    {
        if (!isset($this->modifier)) {
            $this->modifier = $this->getCart()->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING');
        }

        return $this->modifier;
    }

    /**
     * Get address info model
     *
     * @return \XLite\Model\Address
     */
    protected function getAddressInfo()
    {
        $profile = $this->getCart()->getProfile();

        if ($profile && $this->getType() == 'billing') {
            return $profile->getBillingAddress();
        } elseif ($profile && $this->getType() == 'shipping') {
            return $profile->getShippingAddress();
        }

        return null;
    }

    /**
     * Prepare field arguments to create form field widget
     *
     * @param string $name Field name
     * @param array  $data Field data
     *
     * @return array
     */
    protected function getFieldSchemaArgs($name, array $data)
    {
        $data = parent::getFieldSchemaArgs($name, $data);
        $replace = array(
            '[' => '-',
            ']' => '',
            '_' => '-',
        );

        $data['fieldId'] = 'popup_' . strtolower(strtr($this->getFieldFullName($name), $replace));
        return $data;
    }

    /**
     * Get field name
     *
     * @param string $name File short name
     *
     * @return string
     */
    protected function getFieldFullName($name)
    {
        return in_array($name, array('email', 'password'))
            ? $name
            : ($this->getType() . 'Address[' . $name . ']');
    }

    /**
     * Add some data for country_code field
     *
     * @param array $data Array of field data
     *
     * @return array
     */
    protected function prepareFieldParamsCountryCode($data)
    {
        $data[\XLite\View\FormField\Select\Country::PARAM_STATE_SELECTOR_ID] 
            = 'popup_' . $this->getType() . 'address-state-id';
        $data[\XLite\View\FormField\Select\Country::PARAM_STATE_INPUT_ID] 
            = 'popup_' . $this->getType() . 'address-custom-state';

        return $data;
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
            self::PARAM_ADDRESS_TYPE => new \XLite\Model\WidgetParam\TypeString('Address type', null),
        );
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . 'template.twig';
    }

    /**
     * @return string
     */
    public function getDir()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'blocks/popup_address_form/';
    }

    public function getListName($field = null)
    {
        $name = 'checkout_fastlane.blocks.popup_address_form.' . $this->getType();

        if ($field) {
            $name .= '.' . $field;
        }

        return $name;
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . 'style.css';

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

        return $list;
    }


    public function getType()
    {
        return $this->getParam(static::PARAM_ADDRESS_TYPE);
    }
}
