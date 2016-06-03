<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Module settings
 */
class Module extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Module object
     *
     * @var mixed
     */
    protected $module;

    /**
     * Define body classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    public function defineBodyClasses(array $classes)
    {
        $classes = parent::defineBodyClasses($classes);

        $module = $this->getModule();
        if ($module) {
            $classes[] = strtolower('module-' . $module->getAuthor() . '-' . $module->getName());
        }

        return $classes;
    }

    /**
     * handleRequest
     *
     * @return void
     */
    public function handleRequest()
    {
        if (!$this->getModuleID()) {
            $this->setReturnURL($this->buildURL('addons_list_installed'));
        }

        parent::handleRequest();
    }

    /**
     * Return current module options
     *
     * @return array
     */
    public function getOptions()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Config')
            ->findByCategoryAndVisible($this->getModule()->getActualName());
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t(
            'X module settings',
            array(
                'name'   => $this->getModule()->getModuleName(),
            )
        );
    }

    /**
     * Return current module object
     *
     * @return \XLite\Model\Module
     * @throws \Exception
     */
    public function getModule()
    {
        if (!isset($this->module)) {
            $this->module = \XLite\Core\Database::getRepo('\XLite\Model\Module')->find($this->getModuleID());

            if (!$this->module) {
                \XLite\Core\TopMessage::addError('Add-on does not exist.');
                \XLite\Logger::getInstance()->log('Add-on does not exist (ID: ' . $this->getModuleID() . ')', LOG_ERR);

                $this->redirect($this->buildURL('addons_list_installed'));
            }
        }

        return $this->module;
    }

    /**
     * Get current module ID
     *
     * @return integer
     */
    protected function getModuleID()
    {
        return \XLite\Core\Request::getInstance()->moduleId;
    }

    /**
     * Update module settings
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $this->getModelForm()->performAction('update');
    }

    /**
     * getModelFormClass
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return 'XLite\View\Model\ModuleSettings';
    }
}
