<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * Enabled
 */
class Enabled extends \XLite\View\FormField\Input\Checkbox\Simple
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/input/checkbox/enabled.css';

        return $list;
    }

    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return (bool) parent::prepareRequestData($value);
    }

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return parent::isChecked() || true === (bool) $this->getValue();
    }

    /**
     * Get common attributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        $list['value'] = 1;

        return $list;
    }
}
