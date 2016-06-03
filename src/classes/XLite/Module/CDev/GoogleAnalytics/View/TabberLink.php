<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View;

/**
 * Tabber link widget 
 *
 * @ListChild (list="tabs.content", zone="admin")
 */
class TabberLink extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), array('orders_stats'));
    }

    /**
     * Add widget specific CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/GoogleAnalytics/tabs/style.css';

        return $list;
    }
    
    /**
     * Check if the Google Analitics module is configured
     * 
     * @return boolean
     */
    protected function isConfigured()
    {
        return (bool)\XLite\Core\Config::getInstance()->CDev->GoogleAnalytics->ga_account;
    }

    /**
     * Defines the module link to configure
     * 
     * @return string
     */
    protected function getModuleLink()
    {
        return $this->buildURL(
            'module', 
            '',
            array(
                'moduleId' => \XLite\Core\Database::getRepo('XLite\Model\Module')
                    ->findOneBy(array('name' => 'GoogleAnalytics', 'fromMarketplace' => false))->getModuleId(),
            )
        );
    }
    
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/GoogleAnalytics/tabs/link.twig';
    }

}
