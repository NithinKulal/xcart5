<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\CustomerAttachments\Core;

/**
 * Customer attachment static core
 */
class CustomerAttachments extends \XLite\Base\SuperClass
{
    /**
     * Name of input file tag
     */
    const FILES_KEY = 'customer_attachments';

    /**
     * Count of uploaded files in current request
     *
     * @var integer
     */
    protected static $filesUploaded = 0;

    /**
     * Count of attachments is full
     *
     * @var boolean
     */
    protected static $isFull = false;

    /**
     * Attach file from request to order item
     *
     * @param \XLite\Model\OrderItem $item Order item OPTIONAL
     *
     * @return array(\XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment)
     */
    public static function attachFilesFromRequest($item = null)
    {
        $result = array();
        if (isset($_FILES[static::FILES_KEY]['name']) && '' != $_FILES[static::FILES_KEY]['name'][0]) {
            $count = count($_FILES[static::FILES_KEY]['name']);
            for ($position = 0; $position < $count; $position++) {
                $validateResult = static::validateUploadedFile(static::FILES_KEY, $position, $item);

                if ($validateResult) {
                    $attachment = new \XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment();
                    $attachment->setFileName(func_basename($_FILES[static::FILES_KEY]['name'][$position]));

                    $uploadResult = $attachment->loadFromMultipleRequest(static::FILES_KEY, $position);

                    if ($uploadResult) {
                        if (isset($item)) {
                            $attachment->setOrderItem($item);
                            $item->addCustomerAttachments($attachment);
                        }

                        $result[] = $attachment;
                        static::$filesUploaded++;
                    }

                } elseif(static::$isFull) {
                    $allowedFilesQuantityByConfig = \XLite\Core\Config::getInstance()->XC->CustomerAttachments->quantity;

                    \XLite\Core\TopMessage::getInstance()->addError(
                        static::t(
                            'Cannot attach the file. The number of attached files may not exceed X',
                            array('quantity' => $allowedFilesQuantityByConfig)
                        )
                    );

                    break;
                }
            }

            if (count($result) == count($_FILES[static::FILES_KEY]['name'])) {
                \XLite\Core\TopMessage::addInfo(static::t('The files have been attached successfully'));
            } else {
                \XLite\Core\TopMessage::addWarning(static::t('Some files haven`t been attached'));
            }
        }

        return $result;
    }

    public static function deleteAttachment($attachmentId)
    {
        $request = \XLite\Core\Request::getInstance();

        if (isset($attachmentId) && static::isOwner($attachmentId)) {
            \XLite\Core\Database::getRepo('\XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
                ->deleteById($attachmentId, true);
        }
    }

    /**
     * Get allowed to attach files quantity
     *
     * @param \XLite\Model\OrderItem Order item
     *
     * @return integer
     */
    public static function getAllowedQuantity($item = null)
    {
        $allowedFilesQuantity = \XLite\Core\Config::getInstance()->XC->CustomerAttachments->quantity;
        if (isset($item) && $item->getCustomerAttachments()) {
            $currentFilesQuantity = count($item->getCustomerAttachments());
        } else {
            $currentFilesQuantity = static::$filesUploaded;
        }
        $result = $allowedFilesQuantity - $currentFilesQuantity;

        return $result<0 ? 0 : $result;
    }


    /**
     * Get allowed to attach file size in bytes
     *
     * @return integer
     */
    public static function getAllowedSize()
    {
        $config = \XLite\Core\Config::getInstance()->XC->CustomerAttachments;
        $configSize = $config->file_size == 0
            ? PHP_INT_MAX
            : $config->file_size * \XLite\Core\Converter::MEGABYTE;
        $serverSize = \XLite\Core\Converter::convertShortSize(ini_get('upload_max_filesize'));

        return min($configSize, $serverSize);
    }

    /**
     * Get allowed file extensions array
     *
     * @return array
     */
    public static function getAllowedExtensions()
    {
        $config = \XLite\Core\Config::getInstance()->XC->CustomerAttachments;
        $allowedExtensions = $config->extensions
            ? explode(',', preg_replace('/[^,\w+]/', '', $config->extensions))
            : array();

        return $allowedExtensions;
    }

    /**
     * Check file allow to attach
     *
     * @param string                 $key      File key
     * @param integer                $position Position in multiple array
     * @param \XLite\Model\OrderItem $item     Order item
     *
     * @return boolean
     */
    protected static function validateUploadedFile($key, $position, $item)
    {
        $allowedExtensions = static::getAllowedExtensions();
        $allowedFilesQuantity = static::getAllowedQuantity($item);
        $allowedSize = static::getAllowedSize();


        $fileExt = pathinfo($_FILES[$key]['name'][$position], PATHINFO_EXTENSION);
        $fileSize = $_FILES[$key]['size'][$position];

        $supportedExtension = (!$allowedExtensions || in_array($fileExt, $allowedExtensions));
        $result = $supportedExtension
            && $fileSize <= $allowedSize
            && 0<$allowedFilesQuantity
            && ($item ? $item->isCustomerAttachable() : true)
            && ini_get('file_uploads');

        if (!$result) {
            $topMessages = \XLite\Core\TopMessage::getInstance();
            $supportedExtension               ? : $topMessages->addError('Unsupported file extension');
            $fileSize <= $allowedSize         ? : $topMessages->addError(
                static::t(
                    'Cannot attach the file. The maximum attached file size may not exceed X MB',
                    array('size' => round($allowedSize / \XLite\Core\Converter::MEGABYTE, 4))
                )
            );
            if (isset($item)) {
                $item->isCustomerAttachable() ? : $topMessages->addError('File attachments are not allowed for this product');
            }
            ini_get('file_uploads')           ? : $topMessages->addError('File uploads forbidden by web server');

            if (0 >= $allowedFilesQuantity) {
                static::$isFull = true;
            }
        }

        return $result;
    }

    /**
     * Check logged profile is owner attachment
     *
     * @param integer $attachmentId Attachment id
     *
     * @return boolean
     */
    protected function isOwner($attachmentId)
    {
        $attachment = \XLite\Core\Database::getRepo('\XLite\Module\XC\CustomerAttachments\Model\OrderItem\Attachment\Attachment')
            ->find($attachmentId);
        $profile = $attachment->orderItem->order->profile;

        return !\XLite\Core\Auth::getInstance()->isLogged() || \XLite\Core\Auth::getInstance()->checkProfile($profile);
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
} 
