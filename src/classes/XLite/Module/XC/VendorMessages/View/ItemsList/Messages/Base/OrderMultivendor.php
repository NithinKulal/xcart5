<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base;

/**
 * Order messages
 *
 * @Decorator\After ("XC\VendorMessages")
 * @Decorator\Depend ("XC\MultiVendor")
 */
abstract class OrderMultivendor extends \XLite\Module\XC\VendorMessages\View\ItemsList\Messages\Base\Order implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list[static::RESOURCE_JS][] = 'js/core.popup.js';
        $list[static::RESOURCE_JS][] = 'js/core.popup_button.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/VendorMessages/button/open_dispute.js';
        $list[] = 'modules/XC/VendorMessages/popup/dispute.js';

        return $list;
    }

    /**
     * @inheritdoc
     */
    protected function isAllowDispute()
    {
        return \XLite\Module\XC\VendorMessages\Main::isAllowDisputes()
            && !\XLite\Core\Auth::getInstance()->isVendor();
    }

    /**
     * @inheritdoc
     */
    protected function isOpenedDispute()
    {
        return $this->getCurrentThreadOrder()->getIsOpenedDispute();
    }

    /**
     * @inheritdoc
     */
    protected function isAllowWatch()
    {
        return \XLite\Module\XC\VendorMessages\Main::isAllowDisputes()
            && \XLite\Core\Auth::getInstance()->isAdmin()
            && !\XLite\Core\Auth::getInstance()->isVendor()
            && !$this->getOrder()->getIsOpenedDispute();
    }

    /**
     * @inheritdoc
     */
    protected function isRecipientSelectorVisible()
    {
        return \XLite\Module\XC\VendorMessages\Main::isWarehouse()
            && \XLite\Module\XC\VendorMessages\Main::isVendorAllowed()
            && !\XLite\Core\Auth::getInstance()->isVendor()
            && count($this->getOrder()->getChildren()) > 1;
    }

    /**
     * @inheritdoc
     */
    protected function getRecipients()
    {
        $result = parent::getRecipients();
        foreach ($this->getOrder()->getChildren() as $order) {
            if ($order->getVendor()) {
                $result[$order->getOrderId()] = $order->getVendor()->getVendorNameForMessages();

            } else {
                $result[$order->getOrderId()] = $result[0];
            }
        }

        if (
            \XLite\Module\XC\VendorMessages\Main::isWarehouse()
            && \XLite\Module\XC\VendorMessages\Main::isVendorAllowed()
            && isset($result[0])
        ) {
            unset($result[0]);
        }

        return $result;
    }

    /**
     * Get tabs
     *
     * @return array[]
     */
    protected function getTabs()
    {
        $tabs = array();

        $found = false;
        foreach ($this->getRecipients() as $rid => $recipient) {
            $order = $this->getOrder();
            if ($rid != 0 && $rid != $this->getOrder()->getOrderId()) {
                foreach ($this->getOrder()->getChildren() as $o) {
                    if ($o->getOrderId() == $rid) {
                        $order = $o;
                        break;
                    }
                }
            }

            $tab = array(
                'selected'    => $rid == \XLite\Core\Request::getInstance()->recipient_id,
                'url'         => \XLite::isAdminZone()
                    ? static::buildURL('order', null, array('order_number' => $this->getOrder()->getOrderNumber(), 'page' => 'messages', 'recipient_id' => $rid))
                    : static::buildURL('order_messages', null, array('order_number' => $this->getOrder()->getOrderNumber(), 'recipient_id' => $rid)),
                'title'       => $recipient,
                'countUnread' => (\XLite\Core\Auth::getInstance()->isAdmin() && !\XLite\Core\Auth::getInstance()->isVendor())
                    ? $order->countUnreadMessagesForAdmin()
                    : $order->countOwnUnreadMessages(),
                'has_dispute' => $order->getIsOpenedDispute(),
            );
            $tab['marks_visible'] =  $tab['countUnread'] || $tab['has_dispute'];
            if (!$found && $tab['selected']) {
                $found = true;
            }

            $tabs[] = $tab;
        }

        if (!$found && $tabs) {
            $tabs[0]['selected'] = true;
        }

        return $tabs;
    }

    /**
     * Get arguments for dispute label
     *
     * @return string[]
     */
    protected function getDisputeMessageArguments()
    {
        $message = \XLite\Core\Database::getRepo('XLite\Module\XC\VendorMessages\Model\Message')
            ->findOneLastOpenDispute($this->getCurrentThreadOrder());

        return array(
            'date' => $message ? $this->formatDate($message->getDate()) : static::t('n/a'),
            'name' => $message ? $message->getAuthorName() : static::t('n/a'),
        );
    }

    /**
     * Get new message class
     *
     * @return string
     */
    public function getNewMessageClass()
    {
        return $this->getOrder() && $this->getOrder()->isWatchMessages()
            ? 'watch'
            : 'unwatch';
    }
}
