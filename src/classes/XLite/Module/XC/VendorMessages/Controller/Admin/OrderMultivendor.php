<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Controller\Admin;

/**
 * Order page controller
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrderMultivendor extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return \XLite\Core\Request::getInstance()->open_dispute_popup
            ?  static::t('Open a dispute')
            : parent::getTitle();
    }

    /**
     * @inheritdoc
     */
    public function getPages()
    {
        $list = parent::getPages();
        if (
            isset($list['messages'])
            && !\XLite\Module\XC\VendorMessages\Main::isVendorAllowed()
            && \XLite\Core\Auth::getInstance()->isVendor()
        ) {
            unset($list['messages']);
        }

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentThreadOrder()
    {
        $result = parent::getCurrentThreadOrder();

        if (\XLite\Module\XC\VendorMessages\Main::isWarehouse() && \XLite\Module\XC\VendorMessages\Main::isVendorAllowed()) {
            if (\XLite\Core\Auth::getInstance()->isVendor()) {
                if (\XLite\Module\XC\VendorMessages\Main::isVendorAllowed()) {
                    foreach ($result->getChildren() as $order) {
                        if (
                            $order->getVendor()
                            && $order->getVendor()->getProfileId() == \XLite\Core\Auth::getInstance()->getProfile()->getProfileId()
                        ) {
                            $result = $order;
                            break;
                        }
                    }
                }

            } else {
                $found = false;
                $recipientId = intval(\XLite\Core\Request::getInstance()->recipient_id);
                if ($recipientId) {
                    foreach ($result->getChildren() as $order) {
                        if ($order->getOrderId() == $recipientId) {
                            $result = $order;
                            $found = true;
                            break;
                        }
                    }
                }

                if (!$found) {
                    foreach ($result->getChildren() as $order) {
                        $result = $order;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function countUnreadMessages()
    {
        return (\XLite\Core\Auth::getInstance()->isAdmin() && !\XLite\Core\Auth::getInstance()->isVendor())
            ? $this->getOrder()->countUnreadMessagesForAdmin()
            : parent::countUnreadMessages();
    }

    /**
     * Open dispute
     */
    protected function doActionOpenDispute()
    {
        if (\XLite\Core\Request::getInstance()->open_dispute_popup) {
            $this->getModelForm()->performAction('create');
            \XLite\Core\Event::orderMessagesCreate();

        } else {
            $message = $this->createOpenDisputeMessage();
            if ($message) {
                \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')
                    ->insert($message, true);
                \XLite\Core\Event::orderMessagesCreate();
                \XLite\Core\TopMessage::addInfo('A dispute has been opened successfully');
            }
            $this->restoreFormId();
        }
    }

    /**
     * Close dispute
     */
    protected function doActionCloseDispute()
    {
        $message = $this->createCloseDisputeMessage();
        if ($message) {
            \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')
                ->insert($message, true);
            \XLite\Core\Event::orderMessagesCreate();
            \XLite\Core\TopMessage::addInfo('The dispute has been closed');
        }
        $this->restoreFormId();
    }

    /**
     * Watch discussion
     */
    protected function doActionWatchDiscussion()
    {
        $this->getOrder()->setIsWatchMessages(true);
        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Event::orderMessagesWatch();
        \XLite\Core\TopMessage::addInfo(
            'Monitoring of communication related to oder #X has been enabled',
            array('order_number' => $this->getOrder()->getOrderNumber())
        );
        $this->restoreFormId();
    }

    /**
     * Unwatch discussion
     */
    protected function doActionUnwatchDiscussion()
    {
        $this->getOrder()->setIsWatchMessages(false);
        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Event::orderMessagesUnwatch();
        \XLite\Core\TopMessage::addInfo(
            'Monitoring of communication related to oder #X has been disabled',
            array('order_number' => $this->getOrder()->getOrderNumber())
        );
        $this->restoreFormId();
    }

    /**
     * @inheritdoc
     */
    protected function getModelFormClass()
    {
        return $this->getAction() == 'open_dispute' && \XLite\Core\Request::getInstance()->open_dispute_popup
            ? '\XLite\Module\XC\VendorMessages\View\Model\MessageDispute'
            : parent::getModelFormClass();
    }


    /**
     * Create open dispute messages
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    protected function createOpenDisputeMessage()
    {
        /** @var \XLite\Module\XC\VendorMessages\Model\Message $message */
        $message = $this->getCurrentThreadOrder()->buildNewMessage();

        return $message->openDispute() ? $message : null;
    }

    /**
     * Create close dispute messages
     *
     * @return \XLite\Module\XC\VendorMessages\Model\Message
     */
    protected function createCloseDisputeMessage()
    {
        /** @var \XLite\Module\XC\VendorMessages\Model\Message $message */
        $message = $this->getCurrentThreadOrder()->buildNewMessage();
        $message->closeDispute();

        return $message;
    }
}
