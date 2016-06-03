<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Main page controller
 */
class Main extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Define the actions with no secure token
     *
     * @return array
     */
    public static function defineFreeFormIdActions()
    {
        return array_merge(
            parent::defineFreeFormIdActions(),
            array(
                'hide_welcome_block',
                'hide_welcome_block_forever',
                'set_notifications_as_read',
                'close_module_banner',
            )
        );
    }

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return true;
    }

    /**
     * Return 'Taxes' url
     *
     * @return string
     */
    public function getTaxesURL()
    {
        return $this->buildURL('tax_classes');
    }

    /**
     * doActionUpdateInventoryProducts
     *
     * @return void
     */
    protected function doActionUpdateInventoryProducts()
    {
        // Update price and other fields
        \XLite\Core\Database::getRepo('\XLite\Model\Product')
            ->updateInBatchById($this->getPostedData());

        \XLite\Core\TopMessage::addInfo(
            'Inventory has been successfully updated'
        );
    }

    /**
     * Hide welcome block
     *
     * @return void
     */
    protected function doActionHideWelcomeBlock()
    {
        \XLite\Core\Session::getInstance()->hide_welcome_block = 1;

        print ('OK');

        $this->setSuppressOutput(true);
    }

    /**
     * Hide welcome block (forever)
     *
     * @return void
     */
    protected function doActionHideWelcomeBlockForever()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'Internal',
                'name'     => 'hide_welcome_block',
                'value'    => 1,
            )
        );

        print ('OK');

        $this->setSuppressOutput(true);
    }

    /**
     * Hide welcome block (forever)
     *
     * @return void
     */
    protected function doActionSetNotificationsAsRead()
    {
        $types = array(
            'infoMenuReadTimestamp',
            'marketplaceMenuReadTimestamp'
        );
        $menuType = \XLite\Core\Request::getInstance()->menuType;

        if (in_array($menuType, $types, true)) {
            $this->updateMenuReadTimestamp($menuType);
        }

        if ($menuType === 'toplinksMenuReadHash') {
            \XLite\Core\TmpVars::getInstance()->{$menuType} = \XLite\Core\Marketplace::getInstance()->unseenUpdatesHash();
        }

        print ('OK');

        $this->setSuppressOutput(true);
    }

    /**
     * Update menu read timestamp
     *
     * @param string $type Menu type
     */
    protected function updateMenuReadTimestamp($type)
    {
        \XLite\Core\TmpVars::getInstance()->{$type} = LC_START_TIME;
    }

    /**
     * Close module banner
     *
     * @return void
     */
    protected function doActionCloseModuleBanner()
    {
        $moduleName = \XLite\Core\Request::getInstance()->module;
        $closedModuleBanners = \XLite\Core\TmpVars::getInstance()->closedModuleBanners ?: array();
        $closedModuleBanners[$moduleName] = true;

        \XLite\Core\TmpVars::getInstance()->closedModuleBanners = $closedModuleBanners;

        print ('OK');

        $this->setSuppressOutput(true);
    }

    /**
     * Do default action
     *
     * @return void
     */
    protected function doNoAction()
    {
        $this->checkRestorationFlag();
    }

    /**
     * Show TopMessage if shop was recently restored to restore point
     *
     * @return void
     */
    protected function checkRestorationFlag()
    {
        $restored = \Includes\Decorator\Utils\PersistentInfo::get('restoredTo');
        \Includes\Decorator\Utils\PersistentInfo::discard('restoredTo');
        if (!empty($restored)) {
            \XLite\Core\TopMessage::addInfo(
                'The system has been restored to the selected snapshot of X',
                array('date' => \XLite\Core\Converter::formatTime($restored))
            );
        }
    }

    /**
     * Check if current page is accessible for current x-cart license
     *
     * @return boolean
     */
    protected function checkLicense()
    {
        $result = true;
        if ('license_restriction' === \XLite\Core\Request::getInstance()->page
            && \XLite::isFreeLicense()
        ) {
            $result = false;
        }

        return $result;
    }
}
