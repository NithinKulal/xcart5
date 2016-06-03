<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FileAttachments\Model\Product\Attachment;

/**
 * Product attchament's storage 
 *
 * @Entity
 * @Table  (name="product_attachment_storages")
 */
class Storage extends \XLite\Model\Base\Storage
{
    // {{{ Associations

    /**
     * Relation to a attachment
     *
     * @var \XLite\Module\CDev\FileAttachments\Model\Product\Attachment
     *
     * @OneToOne  (targetEntity="XLite\Module\CDev\FileAttachments\Model\Product\Attachment", inversedBy="storage")
     * @JoinColumn (name="attachment_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $attachment;

    // }}}

    // {{{ Service operations

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getValidFileSystemRoot()
    {
        $path = parent::getValidFileSystemRoot();

        if (!file_exists($path . LC_DS . '.htaccess')) {
            file_put_contents(
                $path . LC_DS . '.htaccess',
                'Options -Indexes' . PHP_EOL
                . 'Allow from all' . PHP_EOL
            );
        }

        return $path;
    }

    /**
     * Assemble path for save into DB
     *
     * @param string $path Path
     *
     * @return string
     */
    protected function assembleSavePath($path)
    {
        return $this->getAttachment()->getProduct()->getProductId() . LC_DS . parent::assembleSavePath($path);
    }

    /**
     * Get valid file system storage root
     *
     * @return string
     */
    protected function getStoreFileSystemRoot()
    {
        $path = parent::getStoreFileSystemRoot() . $this->getAttachment()->getProduct()->getProductId() . LC_DS;
        \Includes\Utils\FileManager::mkdirRecursive($path);

        return $path;
    }

    /**
     * Clone for attachment
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment Attachment
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntityForAttachment(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment)
    {
        $newStorage = parent::cloneEntity();

        $attachment->setStorage($newStorage);
        $newStorage->setAttachment($attachment);

        $newStorage->setPath('');

        if (static::STORAGE_URL == $this->getStorageType()) {
            $newStorage->loadFromURL(parent::getURL(), true);

        } else {
            // Clone local image (will be created new file with unique name)
            $newStorage->loadFromLocalFile($this->getStoragePath(), null, true);
        }

        return $newStorage;
    }

    /**
     * Get list of administrator permissions to download files of the storage
     *
     * @return array
     */
    public function getAdminPermissions()
    {
        return array('manage catalog');
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Storage
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     * @return Storage
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * Get fileName
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set mime
     *
     * @param string $mime
     * @return Storage
     */
    public function setMime($mime)
    {
        $this->mime = $mime;
        return $this;
    }

    /**
     * Get mime
     *
     * @return string 
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Set storageType
     *
     * @param string $storageType
     * @return Storage
     */
    public function setStorageType($storageType)
    {
        $this->storageType = $storageType;
        return $this;
    }

    /**
     * Set size
     *
     * @param integer $size
     * @return Storage
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Get size
     *
     * @return integer 
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return Storage
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set attachment
     *
     * @param \XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment
     * @return Storage
     */
    public function setAttachment(\XLite\Module\CDev\FileAttachments\Model\Product\Attachment $attachment = null)
    {
        $this->attachment = $attachment;
        return $this;
    }

    /**
     * Get attachment
     *
     * @return \XLite\Module\CDev\FileAttachments\Model\Product\Attachment 
     */
    public function getAttachment()
    {
        return $this->attachment;
    }
}

