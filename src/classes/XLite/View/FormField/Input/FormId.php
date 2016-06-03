<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Form Identificator specific widget
 */
class FormId extends \XLite\View\FormField\Input\AInput
{
    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_HIDDEN;
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        return \XLite::getFormId();
    }

    /**
     * Return field name
     *
     * @return string
     */
    public function getName()
    {
        return \XLite::FORM_ID;
    }
    
    /**
     * Getter for Field-only flag
     *
     * @return boolean
     */
    protected function getDefaultParamFieldOnly()
    {
        return true;
    }
}
