<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Controller\Customer;

/**
 * Storage
 */
abstract class Storage extends \XLite\Controller\Customer\Storage implements \XLite\Base\IDecorator
{
    /**
     * Get storage
     *
     * @return \XLite\Model\Base\Storage|null
     */
    protected function getStorage()
    {
        if ($this->getCapostLinkId()) {

            // Get storage by a link
            $this->storage = $this->getCapostStorageByLink();

        } else {

            $this->storage = parent::getStorage();
        }

        if (
            isset($this->storage)
            && !$this->checkCapostStorageAccess($this->storage)
        ) {
            // Unauthorized request
            $this->storage = null;
        }

        return $this->storage;
    }

    /**
     * Get storage by a link
     *
     * @return \XLite\Model\Base\Storage|\XLite\Module\XC\CanadaPost\Model\Base\Link|null
     */
    protected function getCapostStorageByLink()
    {
        if (
            !isset($this->storage)
            || !is_object($this->storage)
            || !($this->storage instanceof \XLite\Model\Base\Storage)
        ) {
            $class = \XLite\Core\Request::getInstance()->storage;

            if (\XLite\Core\Operator::isClassExists($class)) {

                $link = \XLite\Core\Database::getRepo($class)->find($this->getCapostLinkId());

                if (isset($link)) {

                    $this->storage = $link->getStorage();

                    if (
                        !isset($this->storage)
                        && $link->callApiGetArtifact(true)
                    ) {
                        // Download artifact
                        $this->storage = $link->getStorage();

                        if (!$this->storage->isFileExists()) {
                            $this->storage = null;
                        }
                    }
                }
            }
        }

        return $this->storage;
    }

    /**
     * Get Canada Post link ID
     *
     * @return mixed
     */
    protected function getCapostLinkId()
    {
        return \XLite\Core\Request::getInstance()->linkId;
    }

    /**
     * Check - is user allowed to get file from storage or not
     *
     * @param \XLite\Model\Base\Storage $storage File model
     *
     * @return boolean
     */
    protected function checkCapostStorageAccess(\XLite\Model\Base\Storage $storage)
    {
        $result = true;

        if (
            $storage instanceof \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Link\Storage
            || $storage instanceof \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest\Link\Storage
        ) {
            // Protect shipment documents from unauthorized requests
            if (!\XLite\Core\Auth::getInstance()->isAdmin()) {
                $result = false;
            }
        }

        if (
            $storage instanceof \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File
            && !$this->checkCapostTrackingFileAccess($storage)
        ) {
            // Protect tracking details documents from unauthorized requests
            $result = false;
        }

        return $result;
    }
    
    /**
     * Check - is tracking file allowed for a user
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File $storage File object 
     *
     * @return boolean
     */
    protected function checkCapostTrackingFileAccess(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Tracking\File $storage)
    {
        $result = false;

        if (
            \XLite\Core\Auth::getInstance()->isLogged()
            && (
                \XLite\Core\Auth::getInstance()->isAdmin()
                || (
                    \XLite\Core\Auth::getInstance()->getProfile()->getProfileId() 
                        == $storage->getTrackingDetails()->getShipment()->getParcel()->getOrder()->getOrigProfile()->getProfileId()
                )
            )
        ) {
            $result = true;
        }

        return $result;
    }
}
