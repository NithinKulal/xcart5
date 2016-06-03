<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Module banner
 */
class ModuleBanner extends \XLite\View\AView
{
    const PARAM_MODULE_NAME = 'moduleName';
    const PARAM_CAN_CLOSE   = 'canClose';

    /**
     * Returns CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'module_banner/style.css';

        return $list;
    }

    /**
     * Returns JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'module_banner/controller.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'module_banner/body.twig';
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
            static::PARAM_MODULE_NAME    => new \XLite\Model\WidgetParam\TypeString('Module name', null),
            static::PARAM_CAN_CLOSE => new \XLite\Model\WidgetParam\TypeBool('Can close', true),
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && !$this->isModuleInstalled()
            && !$this->isBannerClosed();
    }

    /**
     * Check module installed
     *
     * @return boolean
     */
    protected function isModuleInstalled()
    {
        return \Includes\Utils\ModulesManager::isModuleInstalled($this->getModuleName());
    }

    /**
     * Get module name
     *
     * @return string
     */
    protected function getModuleName()
    {
        return $this->getParam(static::PARAM_MODULE_NAME);
    }

    /**
     * Get alphanumeric module name
     *
     * @return string
     */
    protected function getStringModuleName()
    {
        return str_replace('\\', '_', $this->getModuleName());
    }

    /**
     * Check can close
     *
     * @return boolean
     */
    protected function isCanClose()
    {
        return (bool) $this->getParam(static::PARAM_CAN_CLOSE);
    }

    /**
     * Check banner is closed
     *
     * @return boolean
     */
    protected function isBannerClosed()
    {
        $closedModuleBanners = \XLite\Core\TmpVars::getInstance()->closedModuleBanners ?: array();

        return $this->isCanClose() && !empty($closedModuleBanners[$this->getModuleName()]);
    }

    /**
     * Get style class
     *
     * @return string
     */
    protected function getStyleClass()
    {
        return strtolower($this->getStringModuleName());
    }

    /**
     * Returns ACR URL
     *
     * @return string
     */
    protected function getModuleURL()
    {
        list($author, $module) = explode('\\', $this->getModuleName());

        return \XLite\Core\Database::getRepo('XLite\Model\Module')
            ->getMarketplaceUrlByName($author, $module);
    }

    /**
     * Template method
     * 
     * @return string
     */
    protected function getModuleBannerImageUrl()
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath('module_banner/images/QSL_AbandonedCartReminder.png');
    }
}
