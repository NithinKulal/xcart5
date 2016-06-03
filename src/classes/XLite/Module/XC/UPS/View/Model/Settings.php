<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\View\Model;

/**
 * UPS configuration form model
 */
class Settings extends \XLite\View\Model\AShippingSettings
{
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
            case 'dimensions':
                $dimensionUnit = \XLite\Module\XC\UPS\Model\Shipping\Processor\UPS::getDimUnit();
                $cell[static::SCHEMA_LABEL] .= sprintf(' (%s)', $dimensionUnit);
                break;

            case 'max_weight':
                $weightUnit = $this->getWeightSymbol();
                $cell[static::SCHEMA_LABEL] .= sprintf(' (%s)', $weightUnit);
                break;

            case 'extra_cover_value':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'extra_cover' => array(true),
                    ),
                );
                break;

            case 'cod_status':
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_DISABLED] = true;
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_ON_LABEL] = static::t('paymentStatus.Active');
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_OFF_LABEL]
                    = static::t('paymentStatus.Inactive');
                $cell[static::SCHEMA_COMMENT] = static::t(
                    'ups.CODStatusOptionComment',
                    array(
                        'URL' => $this->buildURL('payment_settings')
                    )
                );
                break;

            case 'currency_code':
                $cell[\XLite\View\FormField\Input\Text::PARAM_ATTRIBUTES] = array('readonly' => 'readonly');
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
        switch ($name) {
            case 'dimensions':
                $value = unserialize(parent::getModelObjectValue($name));
                break;

            case 'currency_code':
                $value = $this->getCurrencyCodeByCountry();
                break;

            default:
                $value = parent::getModelObjectValue($name);
                break;
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

        $unEditableOptions = array('cod_status', 'currency_code');
        foreach ($list as $k => $option) {
            if (in_array($option->getName(), $unEditableOptions, true)) {
                unset($list[$k]);
            }
        }

        return $list;
    }

    /**
     * Get currency code by Company country
     *
     * @return string
     */
    protected function getCurrencyCodeByCountry()
    {
        $currencyCode = null;

        $country = \XLite\Core\Database::getRepo('XLite\Model\Country')
            ->findOneByCode(\XLite\Core\Config::getInstance()->Company->origin_country);
        if ($country && $country->getCurrency()) {
            $currencyCode = $country->getCurrency()->getCode();
        }

        return $currencyCode;
    }
}
