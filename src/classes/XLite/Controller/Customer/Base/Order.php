<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Customer\Base;

/**
 * Order
 */
abstract class Order extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Controller parameters
     *
     * @var array
     */
    protected $params = array('profile_id', 'order_number');


    /**
     * Order (cache)
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * Return current order ID
     *
     * @return integer
     */
    protected function getOrderId()
    {
        return intval(\XLite\Core\Request::getInstance()->order_id);
    }

    /**
     * Return current order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        if (!isset($this->order)) {
            if ($this->getOrderId()) {
                $this->order = \XLite\Core\Database::getRepo('XLite\Model\Order')->find($this->getOrderId());

            } elseif (\XLite\Core\Request::getInstance()->order_number) {
                $this->order = \XLite\Core\Database::getRepo('XLite\Model\Order')->findOneByOrderNumber(
                    \XLite\Core\Request::getInstance()->order_number
                );
            }
        }

        return $this->order;
    }

    /**
     * Return current order number
     *
     * @return string
     */
    protected function getOrderNumber()
    {
        return $this->getOrder()->getOrderNumber();
    }

    /**
     * Check if order corresponds to current user
     *
     * @return boolean
     */
    protected function checkOrderProfile()
    {
        return $this->getOrder()
            && $this->getOrder()->getOrigProfile()
            && \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()
                == $this->getOrder()->getOrigProfile()->getProfileId();
    }

    /**
     * Check order access
     *
     * @return boolean
     */
    protected function checkOrderAccess()
    {
        return \XLite\Core\Auth::getInstance()->isLogged() && $this->checkOrderProfile();
    }

    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    protected function checkAccess()
    {
        return parent::checkAccess()
            && $this->getOrder()
            && ($this->checkOrderAccess() || $this->isLastAnonymousOrder() || $this->checkAccessControls());
    }

    /**
     * Return Access control entities for controller as [key => entity]
     *
     * @return \XLite\Model\AEntity[]
     */
    public function getAccessControlEntities()
    {
        return [$this->getOrder()];
    }

    /**
     * Return Access control zones for controller
     *
     * @return \XLite\Model\AEntity[]
     */
    public function getAccessControlZones()
    {
        return ['order'];
    }

    /**
     * Check if requested order has just been placed by the visitor
     *
     * @return boolean
     */
    protected function isLastAnonymousOrder()
    {
        return $this->getOrder()->getOrderId() == \XLite\Core\Session::getInstance()->last_order_id;
    }

    /**
     * Add the base part of the location path
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        $this->addLocationNode(static::t('Search for orders'), $this->buildURL('order_list'));
    }
}
