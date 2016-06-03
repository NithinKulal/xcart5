<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Addon;

/**
 * Install addon popup button
 */
class SelectInstallationType extends \XLite\View\Button\APopupButton
{
    /**
     * Widget param names
     */
    const PARAM_MODULEIDS = 'moduleIds';

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
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
        return array(
            'target'        => 'addon_install',
            'action'        => 'select_installation_type',
            \XLite::FORM_ID => \XLite::getFormId(true),
            'widget'        => '\XLite\View\ModulesManager\InstallationType',
            'moduleIds'     => $this->getModuleIds(),
        );
    }

    /**
     * Define the modules identificators list
     *
     * @return string
     */
    protected function getModuleIds()
    {
        return \XLite\Core\Request::getInstance()->{static::PARAM_MODULEIDS};
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' select-installation-type-button';
    }
}
