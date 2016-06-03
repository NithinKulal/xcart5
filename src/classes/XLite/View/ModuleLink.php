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
class ModuleLink extends \XLite\View\AView
{
    const PARAM_MODULE_NAME = 'moduleName';
    const PARAM_LABEL_TEXT = 'label';
    const PARAM_SHOW_ICON = 'showIcon';

    /**
     * Returns CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'module_link/style.css';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'module_link/body.twig';
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
            static::PARAM_LABEL_TEXT    => new \XLite\Model\WidgetParam\TypeString('Label text', null),
            static::PARAM_SHOW_ICON   => new \XLite\Model\WidgetParam\TypeBool('Show module icon', true),
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
            && !$this->isModuleInstalled();
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
     * Get module name
     *
     * @return string
     */
    protected function isIconVisible()
    {
        return $this->getParam(static::PARAM_SHOW_ICON);
    }

    /**
     * Get label text
     *
     * @return string
     */
    protected function getModuleIcon()
    {
        return $this->isIconVisible()
            ? \XLite\Core\Layout::getInstance()->getResourceWebPath(
                'module_link/icons/' . $this->getStringModuleName() . '.svg'
            )
            : '';
    }

    /**
     * Get label text
     *
     * @return string
     */
    protected function getLabelText()
    {
        return $this->getParam(static::PARAM_LABEL_TEXT);
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
     * Get style class
     *
     * @return string
     */
    protected function getStyleClass()
    {
        return strtolower($this->getStringModuleName());
    }

    /**
     * Returns Module URL
     *
     * @return string
     */
    protected function getModuleURL()
    {
        list($author, $module) = explode('\\', $this->getModuleName());

        return \XLite\Core\Database::getRepo('XLite\Model\Module')
            ->getMarketplaceUrlByName($author, $module);
    }
}
