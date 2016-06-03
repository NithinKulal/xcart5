<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\Controller\Customer;

/**
 * Storage
 */
abstract class Storage extends \XLite\Controller\Customer\Storage implements \XLite\Base\IDecorator
{
    /**
     * Storage private key 
     * 
     * @var   \XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment
     */
    protected $storageKey;

    /**
     * Get storage
     *
     * @return \XLite\Model\Base\Storage
     */
    protected function getStorage()
    {
        $storage = parent::getStorage();

        if (
            $storage
            && $storage instanceof \XLite\Module\CDev\FileAttachments\Model\Product\Attachment\Storage
            && $storage->getAttachment()->getPrivate()
        ) {
            $key = \XLite\Core\Request::getInstance()->key;
            $key = \XLite\Core\Database::getRepo('XLite\Module\CDev\Egoods\Model\OrderItem\PrivateAttachment')
                ->findOneBy(array('downloadKey' => $key));
            if (!$key || $key->getAttachment()->getId() != $storage->getAttachment()->getid() || !$key->isAvailable()) {
                $storage = null;

            } else {
                $this->storageKey = $key;
            }
        }

        return $storage;
    }

    /**
     * Read storage
     *
     * @param \XLite\Model\Base\Storage $storage Storage
     *
     * @return void
     */
    protected function readStorage(\XLite\Model\Base\Storage $storage)
    {
        if ($this->storageKey) {
            $this->storageKey->incrementAttempt();
            \XLite\Core\Database::getEM()->flush();
        }

        parent::readStorage($storage);
    }

}

