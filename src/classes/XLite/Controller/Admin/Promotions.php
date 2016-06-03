<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Admin;

/**
 * Common promotions controller
 */
class Promotions extends \XLite\Controller\Admin\AAdmin
{
    /**
     * FIXME- backward compatibility
     *
     * @var array
     */
    protected $params = array('target', 'page');

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $page = $this->getPage();
        $pages = $this->getPages();

        return !empty($pages[$page]) ? $pages[$page] : static::t('Promotions');
    }

    /**
     * Returns purchase license URL
     *
     * @return string
     */
    public function getPurchaseLicenseURL()
    {
        return \XLite\Core\Marketplace::getPurchaseURL();
    }

    // {{{ Pages

    /**
     * Get pages static
     *
     * @return array
     */
    public static function getPagesStatic()
    {
        $list = array();

        if (\XLite::isFreeLicense()
            && \XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
        ) {
            $list['volume_discounts'] = array(
                'name' => static::t('Volume discounts'),
                'tpl' => 'promotions/volume_discounts.twig',
            );

            $list['coupons'] = array(
                'name' => static::t('Coupons'),
                'tpl' => 'promotions/coupons.twig',
            );
        }

        return $list;
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = array();

        foreach (static::getPagesStatic() as $key => $page) {
            if ($this->checkPageACL($page)) {
                $list[$key] = $page['name'];
            }
        }

        return $list;
    }

    /**
     * Check page permissions and return true if page is allowed
     *
     * @param array $page Page data
     *
     * @return boolean
     */
    protected function checkPageACL($page)
    {
        $result = true;

        if (empty($page['public_access'])
            && !\XLite\Core\Auth::getInstance()->isPermissionAllowed(\XLite\Model\Role\Permission::ROOT_ACCESS)
        ) {
            $result = !empty($page['permission'])
                && \XLite\Core\Auth::getInstance()->isPermissionAllowed($page['permission']);
        }

        return $result;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = array();

        foreach (static::getPagesStatic() as $key => $page) {
            $list[$key] = $page['tpl'];
        }

        return $list;
    }

    // }}}
}
