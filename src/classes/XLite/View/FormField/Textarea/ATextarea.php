<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Textarea;

/**
 * Abstract class for textarea widget
 */
abstract class ATextarea extends \XLite\View\FormField\Input\AInput
{
    /**
     *  Number of rows in textarea widget (HTML attribute)
     */
    const PARAM_ROWS = 'rows';

    /**
     *  Number of columns in textarea widget (HTML attribute)
     */
    const PARAM_COLS = 'cols';


    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_TEXTAREA;
    }

    /**
     * Rows getter
     *
     * @return integer
     */
    public function getRows()
    {
        return $this->getParam(static::PARAM_ROWS);
    }

    /**
     * Columns getter
     *
     * @return integer
     */
    public function getCols()
    {
        return $this->getParam(static::PARAM_COLS);
    }

    /**
     * Return default value of 'rows' HTML attribute.
     *
     * @return integer
     */
    protected function getDefaultRows()
    {
        return 10;
    }

    /**
     * Return default value of 'cols' HTML attribute.
     *
     * @return integer
     */
    protected function getDefaultCols()
    {
        return 50;
    }

    /**
     * getCommonAttributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $attributes = parent::getCommonAttributes() + array(
            static::PARAM_ROWS => $this->getRows(),
            static::PARAM_COLS => $this->getCols(),
        );

        if (isset($attributes['type'])) {
            unset($attributes['type']);
        }

        return $attributes;
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
            static::PARAM_ROWS => new \XLite\Model\WidgetParam\TypeInt('Rows', $this->getDefaultRows()),
            static::PARAM_COLS => new \XLite\Model\WidgetParam\TypeInt('Cols', $this->getDefaultCols()),
        );
    }
}
