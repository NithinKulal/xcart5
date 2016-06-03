<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Form field widget
 */
class FormField extends \XLite\View\AView
{
    /**
     * Widget param names
     */
    const PARAM_FIELD = 'field';


    /**
     * Used in form field components to display a form field according to the 'field' property
     * FIXME - to check
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->getParam(static::PARAM_FIELD);
    }

    /**
     * Return field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getParam(static::PARAM_FIELD);
    }

    /**
     * Return a value for the "id" attribute of the field input tag
     *
     * @return string
     */
    public function getFieldId()
    {
        return strtolower(strtr($this->getName()));
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return null;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_FIELD => new \XLite\Model\WidgetParam\TypeString('Field', null),
        );
    }
}
