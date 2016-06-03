<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * Simple checkbox for inline
 */
class SimpleInline extends \XLite\View\FormField\Input\Checkbox
{
    /**
     * Determines if checkbox is checked
     *
     * @return boolean
     */
    protected function isChecked()
    {
        return $this->getValue() || $this->checkSavedValue();
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();
        $list['value'] = '1';

        return $list;
    }
}
