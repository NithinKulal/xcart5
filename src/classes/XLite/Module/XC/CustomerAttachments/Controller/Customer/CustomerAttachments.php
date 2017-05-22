<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomerAttachments\Controller\Customer;

/**
 * Customer attachments customer controller
 */
class CustomerAttachments extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Id of order item that send request
     *
     * @var integer
     */
    protected $orderItemId;

    /**
     * Order item that send request
     *
     * @var \XLite\Model\OrderItem
     */
    protected $orderItem;

    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        $title = $this->getItem()
            ? $this->getItem()->getName()
            : 'Customer attachments';

        if (!$this->checkAccess()) {
            $title = parent::getTitle();
        }

        return $title;
    }

    /**
     * Get allowed to attach files quantity
     *
     * @return integer
     */
    public function getAllowedQuantity()
    {
        return $this->getItem()
            ? \XLite\Module\XC\CustomerAttachments\Core\CustomerAttachments::getAllowedQuantity($this->getItem())
            : \XLite\Core\Config::getInstance()->XC->CustomerAttachments->quantity;
    }

    /**
     * Get order item
     *
     * @return \XLite\Model\OrderItem
     */
    public function getItem()
    {
        if (empty($this->orderItem)) {
            $this->orderItemId = \XLite\Core\Request::getInstance()->item_id;
            $this->orderItem = \XLite\Core\Database::getRepo('\XLite\Model\OrderItem')->find($this->orderItemId);
        }

        return $this->orderItem;
    }

    /**
     * Get string with allowed extensions for accept attribute
     *
     * @return string
     */
    public function getAcceptExtensionsString()
    {
        $extensions = \XLite\Module\XC\CustomerAttachments\Core\CustomerAttachments::getAllowedExtensions();
        $acceptString = '';
        foreach ($extensions as $ext) {
            $acceptString .= '.' . $ext . ',';
        }
        $acceptString = trim($acceptString, ',');

        return $acceptString;
    }

    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        if (empty(\XLite\Core\Request::getInstance()->item_id)) {
            $this->setReturnURL($this->buildURL('cart'));
            $this->redirect();
        }
    }

    /**
     * Upload file action
     *
     * @return void
     */
    protected function doActionUpload()
    {
        $attachmentModels = \XLite\Module\XC\CustomerAttachments\Core\CustomerAttachments::attachFilesFromRequest($this->getItem());

        if (!empty($attachmentModels)) {
            $em = \XLite\Core\Database::getEM();
            foreach ($attachmentModels as $attachmentModel) {
                $em->persist($attachmentModel);
            }
            $em->flush($attachmentModel);
        }

        $returnURL = $this->buildURL('cart');
        $this->setReturnURL($returnURL);
    }

    /**
     * Upload files from ajax request and print its IDs
     *
     * @return void
     */
    protected function doActionAjaxUpload()
    {
        $attachmentModels = \XLite\Module\XC\CustomerAttachments\Core\CustomerAttachments::attachFilesFromRequest();

        $ids = array();
        foreach ($attachmentModels as $attachment) {
            \XLite\Core\Database::getEM()->persist($attachment);
        }
        \XLite\Core\Database::getEM()->flush();
        foreach ($attachmentModels as $attachment) {
            $ids[] = $attachment->getId();
        }

        $messages = \XLite\Core\TopMessage::getInstance()->getMessages();
        \XLite\Core\TopMessage::getInstance()->clear();

        print json_encode(array('ids' => $ids, 'msg' => $messages));
        exit(0);
    }

    /**
     * Delete attachment action
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $request = \XLite\Core\Request::getInstance();

        \XLite\Module\XC\CustomerAttachments\Core\CustomerAttachments::deleteAttachment($request->attachment_id);

        $this->setInternalRedirect();
        $returnURL = $request->item_id
            ? $this->buildURL('customer_attachments', '', array('item_id' => $request->item_id))
            : $this->buildURL('cart');
        $this->setReturnURL($returnURL);
    }

    /**
     * Logging the data under Customer Attachments
     * Available if developer_mode is on in the config file
     *
     * @param mixed $data
     *
     * @return void
     */
    protected static function log($data)
    {
        if (LC_DEVELOPER_MODE) {
            \XLite\Logger::logCustom('CustomerAttachments', $data);
        }
    }
    /**
     * Check if current page is accessible
     *
     * @return boolean
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && ($this->getItem() || $this->getAction() === 'ajax_upload');
    }
}
