<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Storefront status
 *
 * @ListChild (list="admin.main.page.header", weight="40", zone="admin")
 */
class StorefrontStatus extends \XLite\View\AView
{

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'main_center/page_container_parts/header_parts/storefront_status.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'main_center/page_container_parts/header_parts/storefront_status.twig';
    }

    /**
     * Check - storefront switcher is visible or not
     * 
     * @return boolean
     */
    protected function isTogglerVisible()
    {
        return \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS);
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && \XLite\Core\Auth::getInstance()->isAdmin();
    }

    /**
     * Get container tag attributes 
     * 
     * @return array
     */
    protected function getContainerTagAttributes()
    {
        return array(
            'class' => array(
                'storefront-status',
                (\XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'closed' : 'opened'),
            ),
        );
    }

    /**
     * Get toggler tag attributes 
     * 
     * @return array
     */
    protected function getTogglerTagAttributes()
    {
        return array(
            'class' => array(
                'toggler',
                (\XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'off' : 'on'),
            ),
        );
    }

    /**
     * Get switch link 
     * 
     * @return string
     */
    protected function getLink()
    {
        return $this->buildURL(
            'storefront',
            '',
            array(
                'action'    => (\XLite\Core\Auth::getInstance()->isClosedStorefront() ? 'open' : 'close'),
                'returnURL' => $this->getURL(),
            )
        );
    }

    /**
     * Get accessible shop URL 
     * 
     * @return string
     */
    protected function getOpenedShopURL()
    {
        return \XLite::getController()->getAccessibleShopURL(true);
    }

    /**
     * Get accessible shop URL
     *
     * @return string
     */
    protected function getClosedShopURL()
    {
        return \XLite::getController()->getAccessibleShopURL(false);
    }

    /**
     * Get open title 
     * 
     * @return string
     */
    protected function getOpenTitle()
    {
        return static::t('View storefront');
    }

    /**
     * Get close title 
     * 
     * @return string
     */
    protected function getCloseTitle()
    {
        return static::t(
            'Access storefront via private link',
            array(
                'shop_url' => $this->getClosedShopURL(),
            )
        );
    }

}
