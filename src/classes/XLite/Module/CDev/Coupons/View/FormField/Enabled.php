<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Coupons\View\FormField;

/**
 * Enabled
 */
class Enabled extends \XLite\View\FormField\Input\Checkbox\Simple
{
    /**
     * Prepare request data (typecasting)
     *
     * @param mixed $value Value
     *
     * @return mixed
     */
    public function prepareRequestData($value)
    {
        return (int) parent::prepareRequestData($value);
    }

    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return parent::isChecked() || true === $this->getValue();
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
