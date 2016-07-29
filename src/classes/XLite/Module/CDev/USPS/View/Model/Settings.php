<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\View\Model;

use XLite\Module\CDev\USPS\Model\Shipping\Processor\Usps;

/**
 * USPS configuration form model
 */
class Settings extends \XLite\View\Model\AShippingSettings
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/USPS/style.css';

        return $list;
    }

    /**
     * Detect form field class by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return string
     */
    protected function detectFormFieldClassByOption(\XLite\Model\Config $option)
    {
        return 'dimensions' === $option->getName()
            ? 'XLite\View\FormField\Input\Text\Dimensions'
            : parent:: detectFormFieldClassByOption($option);
    }

    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        switch ($option->getName()) {
            case 'cod_status':
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_DISABLED] = true;
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_ON_LABEL] = static::t('paymentStatus.Active');
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_OFF_LABEL] = static::t('paymentStatus.Inactive');
                $cell[static::SCHEMA_COMMENT] = static::t(
                    'usps.CODStatusOptionComment',
                    array(
                        'URL' => $this->buildURL('payment_settings')
                    )
                );
                break;

            case 'cod_price':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'use_cod_price' => array(true),
                    ),
                );
                break;

            case 'first_class_mail_type':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'use_cod_price' => array(false),
                    ),
                );
                break;

            case 'gxg_pobox':
            case 'gxg_gift':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'gxg' => array(true),
                    ),
                );
                break;
        }

        return $cell;
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        $value = parent::getModelObjectValue($name);
        if ('dimensions' === $name) {
            $value = unserialize($value);
        }

        return $value;
    }

    /**
     * Get editable options
     *
     * @return array
     */
    protected function getEditableOptions()
    {
        $list = parent::getEditableOptions();

        foreach ($list as $k => $option) {

            if ('cod_status' === $option->getName()) {
                unset($list[$k]);

            } elseif ('first_class_mail_type' === $option->getName() && !Usps::isCODPaymentEnabled()) {
                unset($list[$k]);
            }
        }

        return $list;
    }

    /**
     * Validate 'container' field (domestic).
     * Return array (<bool: isValid>, <string: error message>)
     *
     * @param \XLite\View\FormField\AFormField $field Form field object
     * @param array                            $data  List of all fields
     *
     * @return array
     */
    protected function validateFormFieldContainerValue($field, $data)
    {
        $errorMessage = null;

        // Get domestic container type value
        $container = $field->getValue();

        // Get package size value
        $packageSize = isset($data['commonOptionsSeparator'][static::SECTION_PARAM_FIELDS]['package_size'])
            ? $data['commonOptionsSeparator'][static::SECTION_PARAM_FIELDS]['package_size']->getValue()
            : null;

        $isMultiRequests = false;

        if (Usps::isCODPaymentEnabled()) {
            $isMultiRequests = isset($data['cacheOnDeliverySeparator'][static::SECTION_PARAM_FIELDS]['use_cod_price'])
                ? !(bool)$data['cacheOnDeliverySeparator'][static::SECTION_PARAM_FIELDS]['use_cod_price']->getValue()
                : false;
        }

        if ('LARGE' === $packageSize) {
            if (!in_array($container, array('VARIABLE', 'RECTANGULAR', 'NONRECTANGULAR'))) {
                $errorMessage = static::t(
                    'Wrong container type selected: {{value}}. For large package size only the following types are allowed: RECTANGULAR, NONRECTANGULAR, VARIABLE',
                    array('value' => $container)
                );
            }
        }

        if (!$isMultiRequests) {
            // Service ONLINE has limited types of Container values
            if ('REGULAR' === $packageSize && 'VARIABLE' !== $container) {
                $errorMessage = static::t(
                    '{{value}} is an invalid container type for a REGULAR package. Valid Container is: VARIABLE',
                    array('value' => $container)
                );
            }
        }

        return array(empty($errorMessage), $errorMessage);
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        if ($this->isValid()) {
            parent::setModelProperties($data);
        }
    }

    /**
     * Return true if specific section is collapsible
     *
     * @param string $section
     *
     * @return boolean
     */
    protected function isSectionCollapsible($section)
    {
        return false;
    }
}
