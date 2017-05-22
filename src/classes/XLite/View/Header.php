<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Page header
 */
class Header extends \XLite\View\AResourcesContainer
{
    /**
     * Get meta description
     *
     * @return string
     */
    protected function getMetaDescription()
    {
        $result = \XLite::getController()->getMetaDescription();

        return ($result && is_scalar($result))
            ? trim(strip_tags($result))
            : $this->getDefaultMetaDescription();
    }

    /**
     * Get default meta description
     *
     * @return string
     */
    protected function getDefaultMetaDescription()
    {
        return static::t('default-meta-description');
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getTitle()
    {
        return \XLite::getController()->getPageTitle() ?: $this->getDefaultTitle();
    }

    /**
     * Get default title
     *
     * @return string
     */
    protected function getDefaultTitle()
    {
        return static::t('default-site-title');
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'header';
    }

    /**
     * Get collected meta tags
     *
     * @return array
     */
    protected function getMetaResources()
    {
        return \XLite\Core\Layout::getInstance()->getRegisteredMetaTags();
    }

    /**
     * Get script
     *
     * @return string
     */
    protected function getScript()
    {
        return \XLite::getInstance()->getScript();
    }

    /**
     * Get script
     *
     * @return string
     */
    protected function getAdminScript()
    {
        return \XLite::getInstance()->getAdminScript();
    }

    /**
     * Get script
     *
     * @return string
     */
    protected function getCustomerScript()
    {
        return \XLite::getInstance()->getCustomerScript();
    }

    /**
     * Defines the base URL for the page
     * 
     * @return string
     */
    protected function getBaseShopURL()
    {
        return \XLite::getInstance()->getShopURL();
    }
    
    /**
     * Get head tag attributes
     *
     * @return array
     */
    protected function getHeadAttributes()
    {
        return array();
    }

    /**
     *
     */
    public function useUpgradeInsecure()
    {
        return \XLite::getInstance()->getOptions(array('other', 'meta_upgrade_insecure'));
    }
}
