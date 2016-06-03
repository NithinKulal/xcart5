<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button;

/**
 * Activate license key popup button
 */
class ActivateKey extends \XLite\View\Button\APopupButton
{
    /**
     * Widget params
     */
    const PARAM_IS_MODULE = 'isModule';

    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'button/js/activate_key.js';

        return $list;
    }

    /**
     * Register CSS files
     * TODO: should be loaded in popup; remove after loading will be fixed
     * 
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules_manager/activate_key/css/style.css';

        return $list;
    }

    /**
     * Return content for popup button
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Activate your X-Cart';
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
            static::PARAM_IS_MODULE => new \XLite\Model\WidgetParam\TypeInt('Is module activation', 0),
        );
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $params = array(
            'target' => 'activate_key',
            'action' => 'view',
            'widget' => '\XLite\View\ModulesManager\LicenseKey',
            'returnUrl' => \XLite\Core\URLManager::getCurrentURL(),
        );

        if ($this->isModuleActivation()) {
            $params['isModule'] = true;
        }

        return $params;
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' activate-key';
    }

    /**
     * Check if module activation
     *
     * @return boolean
     */
    protected function isModuleActivation()
    {
        return $this->getParam(static::PARAM_IS_MODULE);
    }

    /**
     * Button is visible only if license has been activated
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !\XLite::getXCNLicense();
    }
}
