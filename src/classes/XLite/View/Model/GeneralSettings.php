<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * General settings dialog model widget 
 * (generally it is based on the main settings view model class)
 * 
 */
class GeneralSettings extends \XLite\View\Model\Settings
{
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
     * Check if field is valid and (if needed) set an error message
     *
     * @param array  $data    Current section data
     * @param string $section Current section name
     *
     * @return void
     */
    protected function validateFields(array $data, $section)
    {
        parent::validateFields($data, $section);

        $orderCounterNumber = \XLite\Core\Database::getRepo('XLite\Model\Order')->getMaxOrderNumber() + 1;
        $orderCounterNumberToChange = (int)\XLite\Core\Request::getInstance()->order_number_counter;

        if ($orderCounterNumberToChange < $orderCounterNumber) {
            $this->addErrorMessage(
                static::SECTION_PARAM_FIELDS,
                'The value must be greater than the current maximum order number in the order list'
            );
        }
    }
}
