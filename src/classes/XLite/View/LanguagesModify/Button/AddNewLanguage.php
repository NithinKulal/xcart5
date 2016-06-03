<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\LanguagesModify\Button;

/**
 * Add new language button
 */
class AddNewLanguage extends \XLite\View\Button\Regular
{
    /**
     * Widget parameters
     */
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
            self::PARAM_PAGE => new \XLite\Model\WidgetParam\TypeInt('Page index', 1),
        );
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Add new language';
    }

    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultStyle()
    {
        return 'add-new-language';
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        return 'openAddNewLanguage(this, '
            . '\'' . $this->getParam(self::PARAM_PAGE) . '\');';
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Database::getRepo('\XLite\Model\Language')->findInactiveLanguages();
    }
}
