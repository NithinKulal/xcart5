<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\Controller\Customer;

/**
 * Order messages controller
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
class OrderMessagesMultivendor extends \XLite\Module\XC\VendorMessages\Controller\Customer\OrderMessages implements \XLite\Base\IDecorator
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
    public function getCurrentThreadOrder()
    {
        $result = parent::getCurrentThreadOrder();

        if (\XLite\Module\XC\VendorMessages\Main::isWarehouse() && \XLite\Module\XC\VendorMessages\Main::isVendorAllowed()) {
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

        return $result;
    }

    /**
     * Open dispute
     */
    protected function doActionOpenDispute()
    {
        \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')
            ->insert($this->createOpenDisputeMessage(), true);
        \XLite\Core\Event::orderMessagesCreate();
        \XLite\Core\TopMessage::addInfo('A dispute has been opened successfully');
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
        if (\XLite\Core\Request::getInstance()->body) {
            $message->setBody(\XLite\Core\Request::getInstance()->body);
        }
        $message->openDispute();

        return $message;
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

        return $message->closeDispute() ? $message : null;
    }

}
