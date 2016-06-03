<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager\Action;

/**
 * 'Pack it' action link for Module list (Modules manage)
 *
 * @ListChild (list="itemsList.module.manage.columns.module-main-section.actions", weight="10", zone="admin")
 */
class Main extends \XLite\View\ModulesManager\Action\AAction
{
    /**
     * Widget parameters set
     */
    const PARAM_CAN_ENABLE = 'canEnable';

    /**
     * Defines the name of the action
     *
     * @return string
     */
    public function getName()
    {
        return 'main-action no-disable';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/module/manage/parts/columns/module-main-section/actions/main.twig';
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
            static::PARAM_CAN_ENABLE => new \XLite\Model\WidgetParam\TypeBool('Module can enable flag', false),
        );
    }

    /**
     * Return true if module can not be disabled and section with checkbox 'Enable' should not be displayed
     *
     * @return boolean
     */
    protected function isDisabledHard()
    {
        return !$this->getModule()->getEnabled()
            && !$this->getParam(static::PARAM_CAN_ENABLE);
    }

    /**
     * Return list of attributes for switcher field (used for checkbox element)
     *
     * @param \XLite\Model\Module $module Module object
     *
     * @return array
     */
    protected function getFieldAttributes($module)
    {
        $result = array();

        if ($module->isSkinModule() || $this->isFieldDisabled($module)) {
            $result['disabled'] = 'disabled';
        }

        return $result;
    }

    /**
     * Return true if switcher field is disabled (module state cannot be changed)
     *
     * @param \XLite\Model\Module $module Module object
     *
     * @return boolean
     */
    protected function isFieldDisabled($module)
    {
        return ($module->getEnabled() && !$module->canDisable())
            || (!$module->getEnabled() && !$this->getParam(static::PARAM_CAN_ENABLE));
    }
}
