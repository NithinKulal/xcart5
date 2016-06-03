<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Model\Product\Attachment;

/**
 * Storage
 *
 * @MappedSuperclass
 */
abstract class Storage extends \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage implements \XLite\Base\IDecorator
{
    /**
     * Private suffix length
     */
    const PRIVATE_SUFFIX_LENGTH = 33;

    /**
     * Get URL
     *
     * @return string
     */
    public function getURL()
    {
        return $this->getAttachment()->getPrivate() ? $this->getGetterURL() : parent::getURL();
    }

    /**
     * Get URL for customer front-end
     *
     * @return string
     */
    public function getFrontURL()
    {
        return $this->getAttachment()->getPrivate() ? null : parent::getFrontURL();
    }

    /**
     * Get file extension
     *
     * @return string
     */
    public function getExtension()
    {
        $ext = null;
        if ($this->getAttachment()->getPrivate() && !$this->isURL()) {
            $ext = explode('.', pathinfo($this->getPath(), PATHINFO_FILENAME));
            $ext = $ext[count($ext) - 1];
        }

        return $ext ?: parent::getExtension();
    }

    /**
     * Get download URL for customer front-end by key
     *
     * @return string
     */
    public function getDownloadURL(\XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment $attachment)
    {
        $params = $this->getGetterParams();
        $params['key'] = $attachment->getDownloadKey();

        return \XLite\Core\Converter::buildFullURL('storage', 'download', $params, \XLite::getCustomerScript());
    }

    /**
     * Mask storage
     *
     * @return void
     */
    public function maskStorage()
    {
        $path = $this->getStoragePath();
        $suffix = md5(strval(microtime(true)) . strval(rand(0, 1000000)));
        rename($path, $path . '.' . $suffix);
        $this->setPath($this->getPath() . '.' . $suffix);
    }

    /**
     * Unmask storage
     *
     * @return void
     */
    public function unmaskStorage()
    {
        $path = $this->getStoragePath();
        rename($path, substr($path, 0, static::PRIVATE_SUFFIX_LENGTH * -1));
        $this->setPath(substr($this->getPath(), 0, static::PRIVATE_SUFFIX_LENGTH * -1));
    }

    /**
     * Check - path ir private or not
     *
     * @param string $path Path OPTIONAL
     *
     * @return boolean
     */
    public function isPrivatePath($path = null)
    {
        $path = $path ?: $this->getPath();

        return (bool)preg_match('/\.[a-f0-9]{32}$/Ss', $path);
    }
}

