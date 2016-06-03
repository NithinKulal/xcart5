<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ModulesManager\Action;

use XLite\View\CacheableTrait;

/**
 * Abstract action link for Module list (Modules manage)
 */
abstract class AAction extends \XLite\View\AView
{
    use CacheableTrait;

    /**
     * Widget parameters' names
     */
    const PARAM_MODULE      = 'module';
    const PARAM_MODULE_ID   = 'moduleID';

    /**
     * Module object cache
     *
     * @var \XLite\Model\Module|null
     */
    protected $module = null;

    /**
     * Defines the name of the action
     *
     * @return string
     */
    public function getName()
    {
        return '';
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
            self::PARAM_MODULE => new \XLite\Model\WidgetParam\TypeObject('Module', null, false, '\XLite\Model\Module'),
            self::PARAM_MODULE_ID => new \XLite\Model\WidgetParam\TypeInt('Module ID', 0, false),
        );
    }

    /**
     * Get module
     *
     * @return \XLite\Model\Module
     */
    protected function getModuleObject()
    {
        return $this->getParam(self::PARAM_MODULE_ID)
            ? \XLite\Core\Database::getRepo('XLite\Model\Module')->find($this->getParam(self::PARAM_MODULE_ID))
            : $this->getParam(self::PARAM_MODULE);
    }

    /**
     * Get module
     *
     * @return \XLite\Model\Module
     */
    protected function getModule()
    {
        if (!$this->module) {
            $this->module = $this->getModuleObject();
        }
        return $this->module;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getModule();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = md5(serialize($this->getWidgetParams()));

        return $list;
    }
}
