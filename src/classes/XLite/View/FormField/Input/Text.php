<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

/**
 * Text
 */
class Text extends \XLite\View\FormField\Input\Base\StringInput
{
    const PARAM_SELECT_ON_FOCUS = 'selectOnFocus';
    const PARAM_DO_NOT_TRIM = 'doNotTrim';

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_TEXT;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = $this->getDir() . '/js/text.js';

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
        return $this->getParam(static::PARAM_DO_NOT_TRIM) ? parent::prepareRequestData($value) : trim(parent::prepareRequestData($value));
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_SELECT_ON_FOCUS => new \XLite\Model\WidgetParam\TypeBool('Select on focus', $this->getDefaultSelectOnFocus()),
            static::PARAM_DO_NOT_TRIM => new \XLite\Model\WidgetParam\TypeBool('Do not trim', false),
        );
    }

    /**
     * Should we select all field content on focus
     *
     * @return boolean
     */
    protected function getDefaultSelectOnFocus()
    {
        return false;
    }

    /**
     * Should we select all field content on focus
     *
     * @return boolean
     */
    protected function isSelectOnFocus()
    {
        return $this->getParam(static::PARAM_SELECT_ON_FOCUS);
    }

    /**
     * Register some data that will be sent to template as special HTML comment
     *
     * @return array
     */
    protected function getCommentedData()
    {
        return parent::getCommentedData() + array(
            static::PARAM_SELECT_ON_FOCUS => $this->isSelectOnFocus(),
        );
    }
}
