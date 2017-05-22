<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Controller\Customer;

/**
 * Order messages controller
 */
class OrderMessages extends \XLite\Controller\Customer\Base\Order
{
    /**
     * @inheritdoc
     */
    public function isSecure()
    {
        return \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->checkAccess()
            ? static::t('Messages')
            : static::t('Order not found');
    }

    /**
     * Get current thread order
     *
     * @return \XLite\Model\Order
     */
    public function getCurrentThreadOrder()
    {
        return $this->getOrder();
    }

    /**
     * Add the base part of the location path
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        if ($this->checkAccess()) {
            $this->addLocationNode(
                static::t('Order details'),
                $this->buildURL(
                    'order',
                    null,
                    array('order_number' => $this->getOrderNumber())
                )
            );
        }
    }

    /**
     * @inheritdoc
     */
    protected function getLocation()
    {
        return static::t('Messages');
    }

    /**
     * Update messages list
     */
    protected function doActionUpdate()
    {
        if ($this->needCreateNewMessage()) {
            \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')->insert($this->createNewMessage());
            \XLite\Core\Event::orderMessagesCreate();
        }
    }

    /**
     * Check - need create new message or not
     *
     * @return boolean
     */
    protected function needCreateNewMessage()
    {
        $request = \XLite\Core\Request::getInstance();

        return $request->body;
    }

    /**
     * Create new message
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    protected function createNewMessage()
    {
        $request = \XLite\Core\Request::getInstance();

        /** @var \XLite\Module\XC\VendorMessages\Model\Message $message */
        $message = $this->getCurrentThreadOrder()->buildNewMessage();
        $message->setBody($request->body);

        return $message;
    }

}
