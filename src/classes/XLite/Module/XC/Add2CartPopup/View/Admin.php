<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\View;

/**
 * Add2CartPopup module settings page widget 
 *
 * @ListChild (list="admin.center", zone="admin")
 */
class Admin extends \XLite\View\Dialog
{
    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'add2_cart_popup';

        return $result;
    }

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/style.css';

        return $list;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Add2CartPopup';
    }

    /**
     * Get promotion message
     *
     * @return string
     */
    protected function getPromotionMessage()
    {
        $addons = $this->getAddons();
        $modules = array();

        $params = array('clearCnd' => 1);

        foreach ($addons as $addon => $title) {

            $module = \XLite\Core\Database::getRepo('XLite\Model\Module')->findOneByModuleName($addon, true);

            if (!$module) {
                continue;
            }

            if ($module->getModuleInstalled() && $module->getModuleInstalled()->getEnabled()) {
                continue;
            }

            $url = $module->isInstalled()
                ? $module->getInstalledURL()
                : $module->getMarketplaceURL();

            $modules[] = '<a href="' . $url . '">' . $title . '</a>';
        }

        return (0 < count($modules))
            ? static::t('Install additional modules to add more product sources', array('list' => implode(', ', $modules)))
            : '';
    }

    /**
     * Get modules list which provide additional products sources for 'Add to Cart Popup' dialog
     *
     * @return array
     */
    protected function getAddons()
    {
        return array(
            'XC\Upselling'        => 'Related Products',
            'CDev\ProductAdvisor' => 'Product Advisor',
        );
    }
}
