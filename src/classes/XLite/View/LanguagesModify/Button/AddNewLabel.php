<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguagesModify\Button;

/**
 * Add new label button
 */
class AddNewLabel extends \XLite\View\Button\Regular
{
    /**
     * Widget parameters
     */
    const PARAM_LANGUAGE = 'language';
    const PARAM_PAGE = 'page';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_LANGUAGE => new \XLite\Model\WidgetParam\TypeString('Language code', null),
            self::PARAM_PAGE     => new \XLite\Model\WidgetParam\TypeInt('Page index', 1),
        );
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Add new label';
    }

    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'add-new-label';
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return 'openAddNewLabel(this, '
            . '\'' . $this->getParam(self::PARAM_LANGUAGE) . '\', '
            . '\'' . $this->getParam(self::PARAM_PAGE) . '\');';
    }

    /**
     * Get class
     *
     * @return string
     */
    protected function getClass()
    {
        return trim(parent::getClass() . ' always-reload');
    }

}
