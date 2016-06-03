<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model;

/**
 * Order
 */
abstract class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Get Pivate attachments list
     *
     * @return array
     */
    public function getPrivateAttachments()
    {
        $list = array();
        foreach ($this->getItems() as $item) {
            $list = array_merge($list, $item->getPrivateAttachments()->toArray());
        }

        return $list;
    }

    /**
     * Get downloadable Pivate attachments list
     *
     * @return array
     */
    public function getDownloadAttachments()
    {
        $list = array();
        foreach ($this->getItems() as $item) {
            $list = array_merge($list, $item->getDownloadAttachments());
        }

        return $list;
    }

    /**
     * Called when an order successfully placed by a client
     *
     * @return void
     */
    public function processSucceed()
    {
        parent::processSucceed();

        foreach ($this->getItems() as $item) {
            $item->createPrivateAttachments();
        }
    }

    /**
     * A "change status" handler
     *
     * @return void
     */
    protected function processProcess()
    {
        foreach ($this->getPrivateAttachments() as $attachment) {
           $attachment->renew();
        }

        parent::processProcess();
    }
}
