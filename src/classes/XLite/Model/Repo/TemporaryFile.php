<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Repo;

/**
 * Temporary file repository
 */
class TemporaryFile extends \XLite\Model\Repo\Base\Image
{
    /**
     * Get storage name
     *
     * @return string
     */
    public function getStorageName()
    {
        return 'temporary';
    }

    /**
     * Get file system images storage root path
     *
     * @return string
     */
    public function getFileSystemRoot()
    {
        return LC_DIR_CACHE_IMAGES . $this->getStorageName() . LC_DS;
    }

    /**
     * Get web images storage root path
     *
     * @return string
     */
    public function getWebRoot()
    {
        return LC_IMAGES_CACHE_URL . '/' . $this->getStorageName() . '/';
    }
}
