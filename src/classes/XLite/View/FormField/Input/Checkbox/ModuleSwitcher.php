<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Checkbox;

/**
 * Module state switcher form field
 */
class ModuleSwitcher extends \XLite\View\FormField\Input\Checkbox\Switcher
{
    const PARAM_WARNING_ICON = 'warningIcon';
    const PARAM_COMMENT_TEXT = 'commentText';
    const PARAM_IS_READ_ONLY = 'isReadOnly';
    const PARAM_HELP_ID      = 'helpId';
    const PARAM_MODULE_ID    = 'moduleId';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_WARNING_ICON => new \XLite\Model\WidgetParam\TypeString('Warning icon', 'fa-exclamation-circle'),
            self::PARAM_COMMENT_TEXT => new \XLite\Model\WidgetParam\TypeString('Switcher tooltip text', ''),
            self::PARAM_IS_READ_ONLY => new \XLite\Model\WidgetParam\TypeBool('Switcher read-only status', false),
            self::PARAM_MODULE_ID    => new \XLite\Model\WidgetParam\TypeInt('Module ID', null),
            self::PARAM_HELP_ID      => new \XLite\Model\WidgetParam\TypeString('ID of element containing help text for tooptip', ''),
        );
    }

    /**
     * Register CSS class to use for wrapper block (SPAN) of input field.
     * It is usable to make unique changes of the field.
     *
     * @return string
     */
    public function getWrapperClass()
    {
        return trim(parent::getWrapperClass()
            . ($this->isSwitcherReadOnly() ? ' read-only' : '')
            . ($this->getValue() ? ' disable' : ' enable')
        );
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/checkbox/module_switcher.twig';
    }

    /**
     * Get module ID
     *
     * @return string
     */
    protected function getModuleId()
    {
        return $this->getParam(self::PARAM_MODULE_ID);
    }

    /**
     * Get old name
     *
     * @return string
     */
    protected function getOldName()
    {
        return sprintf('switch[%d][old]', $this->getModuleId());
    }

    /**
     * Get warning icon class
     *
     * @return string
     */
    protected function getWarningIcon()
    {
        return $this->getParam(self::PARAM_WARNING_ICON);
    }

    /**
     * Return true if switcher in read-only mode
     *
     * @return boolean
     */
    protected function isSwitcherReadOnly()
    {
        return $this->getParam(self::PARAM_IS_READ_ONLY);
    }

    /**
     * Get ID of element containing help text for tooltip
     *
     * @return string
     */
    protected function getHelpId()
    {
        return $this->getParam(self::PARAM_HELP_ID);
    }
}
