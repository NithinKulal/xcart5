<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\Controller\Admin;

/**
 * Menus controller
 *
 */
class Menus extends \XLite\Controller\Admin\AAdmin
{
    /**
     * FIXME- backward compatibility
     *
     * @var array
     */
    protected $params = array('target', 'page');

    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage menus');
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        return \XLite\Module\CDev\SimpleCMS\Model\Menu::getTypes();
    }

    /**
     * Get current page
     *
     * @return string
     */
    public function getPage()
    {
        return \XLite\Core\Request::getInstance()->page ?: \XLite\Module\CDev\SimpleCMS\Model\Menu::MENU_TYPE_PRIMARY;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        $menuItem = \XLite\Core\Database::getRepo('XLite\Module\CDev\SimpleCMS\Model\Menu')
                ->find(intval(\XLite\Core\Request::getInstance()->id));
        return $menuItem
            ? $menuItem->getName()
            : static::t('Menus');
    }

    /**
     * Check if the option "Show default menu along with the custom one" is displayed
     *
     * @return boolean
     */
    public function isVisibleShowDefaultOption()
    {
        return false;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = array();
        foreach (\XLite\Module\CDev\SimpleCMS\Model\Menu::getTypes() as $k => $v) {
            $list[$k] = 'modules/CDev/SimpleCMS/menus/body.twig';
        }

        return $list;
    }
}
