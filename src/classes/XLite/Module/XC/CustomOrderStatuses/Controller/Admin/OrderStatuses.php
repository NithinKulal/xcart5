<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\Controller\Admin;

/**
 * Custom order statuses controller
 *
 */
class OrderStatuses extends \XLite\Controller\Admin\AAdmin
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
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('manage orders');
    }

    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        return array(
            'payment'  => 'Payment statuses',
            'shipping' => 'Shipping statuses',
        );
    }

    /**
     * Get current page
     *
     * @return string
     */
    public function getPage()
    {
        $page = \XLite\Core\Request::getInstance()->page;
        $pages = $this->getPages();

        return $page && isset($pages[$page]) ? $page : 'payment';
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Order statuses');
    }

    /**
     * Return items list class
     *
     * @return string
     */
    public function getItemsListClass()

    {
        return '\XLite\Module\XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\\' . ucfirst($this->getPage());
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = array();
        foreach ($this->getPages() as $k => $v) {
            $list[$k] = 'modules/XC/CustomOrderStatuses/statuses/body.twig';
        }

        return $list;
    }
}