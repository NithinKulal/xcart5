<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Controller\Admin;

use \XLite\Module\XC\MailChimp\Core;

/**
 * Shopgate connector module settings
 */
class MailchimpOptions extends \XLite\Controller\Admin\Module
{
    /**
     * Get current module ID
     *
     * @return integer
     */
    public function handleRequest()
    {
        parent::handleRequest();

        $sections = Core\MailChimpSettings::getInstance()->getAllSections();

        if (!in_array(\XLite\Core\Request::getInstance()->section, $sections)) {

            $this->setHardRedirect();

            $this->setReturnURL(
                $this->buildURL(
                    'mailchimp_options',
                    '',
                    array(
                        'section'  => $this->getCurrentSection(),
                    )
                )
            );

            $this->doRedirect();
        }
    }

    /**
     * Get current module ID
     *
     * @return integer
     */
    protected function getModuleID()
    {
        if (!isset($this->moduleID)) {
            $module = \XLite\Core\Database::getRepo('\XLite\Model\Module')->findOneBy(
                array(
                    'name'      => 'MailChimp',
                    'author'    => 'XC',
                    'installed' => 1,
                    'enabled'   => 1
                )
            );

            if ($module) {
                \XLite\Core\Request::getInstance()->moduleId = $module->getModuleID();
                $this->moduleID = $module->getModuleID();
                $this->module = $module;
            }
        }

        return $this->moduleID;
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string
     */
    protected function getModelFormClass()
    {
        return '\XLite\Module\XC\MailChimp\View\Model\ModuleSettings';
    }

    /**
     * Get current section
     *
     * @return string
     */
    protected function getCurrentSection()
    {
        $return = \XLite\Core\Request::getInstance()->section;

        if (!in_array($return, Core\MailChimpSettings::getInstance()->getAllSections())) {
            $return = Core\MailChimpSettings::SECTION_MAILCHIMP_API;
        }

        return $return;
    }
}
