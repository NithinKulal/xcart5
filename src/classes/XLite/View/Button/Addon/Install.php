<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Addon;

/**
 * Install addon popup button
 * 
 * @TODO to remove
 */
class Install extends \XLite\View\Button\APopupButton
{
    /**
     * Widget param names
     */
    const PARAM_MODULEID          = 'moduleId';
    const PARAM_PAYMENT_METHOD_ID = 'paymentMethodId';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        // :TODO: must be taken from LICENSE module widget
        $list[] = 'modules_manager/license/css/style.css';
        $list[] = 'modules_manager/warnings/css/style.css';

        // :TODO: must be taken from SwitchButton widget
        $list[] = \XLite\View\Button\SwitchButton::SWITCH_CSS_FILE;

        $list[] = 'modules_manager/installation_type/css/style.css';

        return $list;
    }

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/install_addon.js';

        // :TODO: must be taken from LICENSE module widget
        $list[] = 'modules_manager/license/js/switch-button.js';

        // :TODO: must be taken from SwitchButton widget
        $list[] = \XLite\View\Button\SwitchButton::JS_SCRIPT;

        $list[] = 'button/js/select_installation_type.js';

        return $list;
    }

    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Install';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $params = array(
            'target'   => \XLite\View\ModulesManager\ModuleLicense::MODULE_LICENSE_TARGET,
            'action'   => 'view_license',
            'widget'   => '\XLite\View\ModulesManager\ModuleLicense',
            'moduleId' => $this->getParam(static::PARAM_MODULEID),
        );

        if ($this->getParam(static::PARAM_PAYMENT_METHOD_ID)) {
            $params[static::PARAM_PAYMENT_METHOD_ID] = $this->getParam(static::PARAM_PAYMENT_METHOD_ID);
        }

        return $params;
    }

    /**
     * Define widgets parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_MODULEID          => new \XLite\Model\WidgetParam\TypeString('ModuleId', '', true),
            static::PARAM_PAYMENT_METHOD_ID => new \XLite\Model\WidgetParam\TypeInt('Payment method ID', null),
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' install-addon-button';
    }
}
