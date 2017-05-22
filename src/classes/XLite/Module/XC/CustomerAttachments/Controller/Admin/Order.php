<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\CustomerAttachments\Controller\Admin;

/**
 * Decorate order update
 */
class Order extends \XLite\Controller\Admin\Order implements \XLite\Base\IDecorator
{
    /**
     * Order history description pattern
     */
    const TXT_CUSTOMER_ATTACHMENT_DESCRIPTION = 'Customer`s attachments were changed by X';

    /**
     * Changes history for customer attachments
     *
     * @var array
     */
    protected static $customerAttachmentsChanges = array();

    /**
     * doActionUpdate
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        parent::doActionUpdate();

        $toDeleteAttachments = \XLite\Core\Request::getInstance()->delete_attachment;

        if (isset($toDeleteAttachments) && is_array($toDeleteAttachments)) {
            foreach ($toDeleteAttachments as $id=>$value) {
                $attachmentModel = \XLite\Core\Database::getRepo('\XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                    ->find($id);
                $productName = $attachmentModel->getOrderItem()->getName();
                $attachmentName = $attachmentModel->getFileName();

                \XLite\Core\Database::getRepo('\XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                    ->deleteById($id, true);

                $msg = static::t('Attachment X is deleted', array('filename' => $attachmentName));
                $this->setAttachmentsOrderChanges($productName, $msg);
            }

            $description = static::t(
                static::TXT_CUSTOMER_ATTACHMENT_DESCRIPTION,
                array(
                    'user' => \XLite\Core\Auth::getInstance()->getProfile() ? \XLite\Core\Auth::getInstance()->getProfile()->getLogin() : 'unknown'
                )
            );
            \XLite\Core\OrderHistory::getInstance()->registerEvent(
                $this->getOrder()->getOrderId(),
                \XLite\Core\OrderHistory::CODE_ORDER_EDITED,
                $description,
                array(),
                serialize(static::$customerAttachmentsChanges)
            );
        }
    }

    /**
     * Set changes info about attachment
     *
     * @param $productName
     * @param $msg
     */
    protected function setAttachmentsOrderChanges($productName, $msg)
    {
        static::$customerAttachmentsChanges[$productName][] = array(
            'old' => '',
            'new' => $msg,
        );
    }
} 