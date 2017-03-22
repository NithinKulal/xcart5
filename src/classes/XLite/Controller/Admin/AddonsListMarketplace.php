<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * AddonsListMarketplace
 */
class AddonsListMarketplace extends \XLite\Controller\Admin\Base\AddonsList
{
    /**
     * Cache of landing page availability
     *
     * @var null | boolean
     */
    protected $landingPageAvail = null;

    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        $list = parent::defineFreeFormIdActions();
        $list[] = 'clear_cache';
        $list[] = 'set_install';
        $list[] = 'unset_install';

        return $list;
    }

    /**
     * Clean the modules-to-install list. It is used right before the installation starts.
     *
     * @return void
     */
    public static function cleanModulesToInstall()
    {
        \XLite\Core\Session::getInstance()->modulesToInstall = array();
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getModuleId()
            ? $this->getModuleName($this->getModuleId())
            : static::t('Modules Marketplace');
    }

    /**
     * The landing page flag
     *
     * @return boolean
     */
    public function isLandingPage()
    {
        if (is_null($this->landingPageAvail)) {
            $landingPageAvail = $this->isMarketplaceAccessible()
                ? \XLite\Core\Marketplace::getInstance()->isAvailableLanding()
                : null;

            // Landing page is unavailable if no modules are set on the landing page
            $isLandingModules = (bool)\XLite\Core\Database::getRepo('XLite\Model\Module')
                ->findOneBy(array('isLanding' => true));

            $this->landingPageAvail = isset($landingPageAvail[\XLite\Core\Marketplace::FIELD_LANDING])
                ? (bool)$landingPageAvail[\XLite\Core\Marketplace::FIELD_LANDING] && $isLandingModules
                : false;
        }

        return $this->landingPageAvail && isset(\XLite\Core\Request::getInstance()->landing);
    }

    /**
     * Get the module id list from the session
     *
     * @return array
     */
    public function getModulesToInstall()
    {
        \XLite\Core\Session::getInstance()->modulesToInstall = (!\XLite\Core\Session::getInstance()->modulesToInstall) || $this->getModuleId()
            ? array()
            : array_filter(
                \XLite\Core\Session::getInstance()->modulesToInstall,
                array($this, 'checkModulesToInstall')
            );

        return \XLite\Core\Session::getInstance()->modulesToInstall;
    }

    /**
     * Simple check if module id is valid (if there is any module in DB with such moduleId)
     * It is used in self::getModulesToInstall() method
     *
     * @see self::getModulesToInstall()
     *
     * @param integer|string $moduleId Module identificator
     *
     * @return boolean
     */
    public function checkModulesToInstall($moduleId)
    {
        return (bool)\XLite\Core\Database::getRepo('XLite\Model\Module')->find($moduleId);
    }

    /**
     * Verifies if the module is selected to install
     *
     * @param integer $moduleId
     *
     * @return boolean
     */
    public function isModuleSelected($moduleId)
    {
        return isset(\XLite\Core\Session::getInstance()->modulesToInstall[$moduleId]);
    }

    /**
     * Returns the number of selected modules
     *
     * @return integer
     */
    public function countModulesSelected()
    {
        return count(\XLite\Core\Session::getInstance()->modulesToInstall);
    }

    /**
     * Checks if the modules selected list is not empty
     *
     * @return boolean
     */
    public function hasModulesSelected()
    {
        return $this->countModulesSelected() > 0;
    }

    /**
     * Empty tag is provided for default landing page
     *
     * @return string
     */
    public function getTagValue()
    {
        return '';
    }

    /**
     * Get module id from request
     *
     * @return integer
     */
    public function getModuleId()
    {
        $moduleID = \XLite\Core\Request::getInstance()->moduleID;
        $moduleName = \XLite\Core\Request::getInstance()->moduleName;

        $repo = \XLite\Core\Database::getRepo('XLite\Model\Module');
        if (!$moduleID && $moduleName && strpos($moduleName, '\\')) {
            $module = $repo->findOneByModuleName($moduleName, true);
            $moduleID = $module ? $module->getModuleId() : null;
        }

        return $moduleID && $repo->find($moduleID)
            ? $moduleID
            : null;
    }

    /**
     * Return module full name
     *
     * @param integer $id
     *
     * @return string
     */
    public function getModuleName($id)
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Module')->find($id)->getModuleName();
    }

    /**
     * Clear marketplace cache
     *
     * @return void
     */
    protected function doActionClearCache()
    {
        \XLite\Core\Marketplace::getInstance()->clearActionCache();

        $params = \XLite\Core\Request::getInstance()->landing
            ? array('landing' => 1)
            : array();

        $this->setReturnURL($this->buildURL('addons_list_marketplace', '', $params));
    }

    /**
     * Request for upgrade
     *
     * @return void
     */
    protected function doActionRequestForUpgrade()
    {
        $module = \XLite\Core\Database::getRepo('XLite\Model\Module')
            ->find(\XLite\Core\Request::getInstance()->module);

        if (!$module || !\XLite\Core\Marketplace::getInstance()->isUpgradeRequestAvailable($module)) {
            \XLite\Core\TopMessage::addWarning('An error occurred while sending the request');
            $this->valid = false;
            return;
        }

        $result = \XLite\Core\Marketplace::getInstance()->requestForUpgrade([
            $module->getMarketplaceID() => $module,
        ]);

        if (!empty($result)) {
            \XLite\Core\Marketplace::getInstance()->markAsUpgradeRequested($module);
            \XLite\Core\TopMessage::addInfo('Your request has been sent successfully');

        } else {
            \XLite\Core\TopMessage::addWarning('An error occurred while sending the request');
            $this->valid = false;
        }
    }

    /**
     * Store the module id for installation
     *
     * @return void
     */
    protected function doActionSetInstall()
    {
        $this->storeModuleToInstall(\XLite\Core\Request::getInstance()->id);
        exit(0);
    }

    /**
     * Remove the module id for the installation
     *
     * @return void
     */
    protected function doActionUnsetInstall()
    {
        $this->removeModuleToInstall(\XLite\Core\Request::getInstance()->id);
        exit(0);
    }

    /**
     * Store the module id into the session for the further installation
     *
     * @param integer $id
     *
     * @return void
     */
    protected function storeModuleToInstall($id)
    {
        if (!\XLite\Core\Session::getInstance()->modulesToInstall) {
            \XLite\Core\Session::getInstance()->modulesToInstall = array();
        }

        \XLite\Core\Session::getInstance()->modulesToInstall =
            \XLite\Core\Session::getInstance()->modulesToInstall + array($id => $id);
    }

    /**
     * Remove the module id from the installation list
     *
     * @param integer $id
     *
     * @return void
     */
    protected function removeModuleToInstall($id)
    {
        if (!\XLite\Core\Session::getInstance()->modulesToInstall) {
            \XLite\Core\Session::getInstance()->modulesToInstall = array();
        }

        if (isset(\XLite\Core\Session::getInstance()->modulesToInstall[$id])) {
            $modulesToInstall = \XLite\Core\Session::getInstance()->modulesToInstall;
            unset($modulesToInstall[$id]);
            \XLite\Core\Session::getInstance()->modulesToInstall = $modulesToInstall;
        }
    }
}
