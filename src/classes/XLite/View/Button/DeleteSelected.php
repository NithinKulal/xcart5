<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * 'Delete selecetd' button
 */
class DeleteSelected extends \XLite\View\Button\Regular
{
    /**
     * Widget parameter names
     */
    const PARAM_CONFIRMATION = 'confirm';

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Delete selected';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        return static::t('Delete selected');
    }

    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'delete';
    }

    /**
     * getDefaultConfirmationText
     *
     * @return string
     */
    protected function getDefaultConfirmationText()
    {
        return 'Do you really want to delete selected items?';
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
            self::PARAM_CONFIRMATION => new \XLite\Model\WidgetParam\TypeString(
                'Confirmation text', $this->getDefaultConfirmationText(), true
            ),
        );
    }

    /**
     * JavaScript: default JS code to execute
     *
     * @return string
     */
    protected function getDefaultJSCode()
    {
        $code = parent::getDefaultJSCode();

        if ($this->getParam(self::PARAM_CONFIRMATION)) {
            $code = 'if (confirm(\'' . static::t($this->getParam(self::PARAM_CONFIRMATION)) . '\')) { ' . $code . ' }';
        }

        return $code;
    }
}
