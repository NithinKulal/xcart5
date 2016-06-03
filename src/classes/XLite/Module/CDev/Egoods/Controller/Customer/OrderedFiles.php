<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Controller\Customer;

/**
 * Ordered files
 */
class OrderedFiles extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Define current location for breadcrumbs
     *
     * @return string
     */
    protected function getLocation()
    {
        return static::t('Ordered files');
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('My account'));
    }

    /**
     * Check - controller must work in secure zone or not
     *
     * @return boolean
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Ordered files');
    }

    /**
     * Check whether the title is to be displayed in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return \XLite\Core\Request::getInstance()->widget;
    }

    /**
     * Get orders with files
     *
     * @return array
     */
    public function getOrdersWithFiles()
    {
        return \XLite\Core\Database::getRepo('\XLite\Model\Order')->findAllOrdersWithEgoods($this->getProfile());
    }
}
