<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * \XLite\View\FormField\Input\Checkbox\ShipAsBill
 */
class ShipAsBill extends \XLite\View\FormField\Input\Checkbox
{
    /**
     * Return a value for the "id" attribute of the field input tag
     *
     * @return string
     */
    public function getFieldId()
    {
        return 'ship-as-bill';
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/ship_as_bill.js';

        return $list;
    }


    /**
     * getDefaultValue
     *
     * @return string
     */
    protected function getDefaultValue()
    {
        return true;
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'The same as billing';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/ship_as_bill.twig';
    }

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return parent::isChecked() || $this->callFormMethod('getShipAsBillFlag');
    }
}
