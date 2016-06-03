<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\View\Model;

/**
 * FedEx configuration form model
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
            case 'cod_status':
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_DISABLED] = true;
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_ON_LABEL] = static::t('paymentStatus.Active');
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_OFF_LABEL] = static::t('paymentStatus.Inactive');
                $cell[static::SCHEMA_COMMENT] = static::t(
                    'fedex.CODStatusOptionComment',
                    array(
                        'URL' => $this->buildURL('payment_settings')
                    )
                );
                break;

            case 'fxsp_hub_id':
            case 'fxsp_indicia':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'fxsp' => array(true),
                    ),
                );
                break;

            case 'dg_accessibility':
                $cell[static::SCHEMA_DEPENDENCY] = array(
                    static::DEPENDENCY_SHOW => array(
                        'fxsp' => array(false),
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
            }
        }

        return $list;
    }
}
