<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Base;

/**
 * Image abstract store
 *
 * @MappedSuperclass
 * @HasLifecycleCallbacks
 */
abstract class Image extends \XLite\Model\Base\Storage
{
    /**
     * MIME type to extension translation table
     *
     * @var array
     */
    protected static $types = array(
        'image/jpeg'            => 'jpeg',
        'image/jpg'             => 'jpg',
        'image/gif'             => 'gif',
        'image/xpm'             => 'xpm',
        'image/gd'              => 'gd',
        'image/gd2'             => 'gd2',
        'image/wbmp'            => 'bmp',
        'image/bmp'             => 'bmp',
        'image/x-ms-bmp'        => 'bmp',
        'image/x-windows-bmp'   => 'bmp',
        'image/png'             => 'png',
    );

    /**
     * Width
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $width = 0;

    /**
     * Height
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $height = 0;

    /**
     * Image hash
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=32, nullable=true)
     */
    protected $hash;

    /**
     * Is image need process or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $needProcess = true;

    /**
     * Check file is image or not
     *
     * @return boolean
     */
    public function isImage()
    {
        return true;
    }

    /**
     * Get image URL for customer front-end
     *
     * @return string
     */
    public function getFrontURL()
    {
        return (!$this->getRepository()->isCheckImage() || $this->checkImageHash()) ? parent::getFrontURL() : null;
    }

    /**
     * Check - image hash is equal data from DB or not
     *
     * @return boolean
     */
    public function checkImageHash()
    {
        $result = true;

        if ($this->getHash()) {
            list($path, $isTempFile) = $this->getLocalPath();

            $hash = \Includes\Utils\FileManager::getHash($path);

            if ($isTempFile) {
                \Includes\Utils\FileManager::deleteFile($path);
            }

            $result = $this->getHash() === $hash;
        }

        return $result;
    }

    /**
     * Check - image is exists in DB or not
     * TODO - remove - old method
     *
     * @return boolean
     */
    public function isExists()
    {
        return !is_null($this->getId());
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newEntity = parent::cloneEntity();

        $newEntity->setPath('');

        if (static::STORAGE_URL == $this->getStorageType()) {
            $newEntity->loadFromURL($this->getURL(), true);

        } else {
            // Clone local image (will be created new file with unique name)
            $newEntity->loadFromLocalFile($this->getStoragePath(), null, true);
        }

        return $newEntity;
    }

    /**
     * Update file path - change file extension taken from MIME information.
     *
     * @return boolean
     */
    protected function updatePathByMIME()
    {
        $result = parent::updatePathByMIME();

        if ($result && !$this->isURL()) {
            list($path, ) = $this->getLocalPath();

            $newExtension = $this->getExtensionByMIME();
            $pathinfo = pathinfo($path);

            // HARDCODE for BUG-2520
            if (strtolower($pathinfo['extension']) == 'jpg' && $newExtension == 'jpeg') {
                $newExtension = 'jpg';
            }

            if ($newExtension !== $pathinfo['extension']) {
                $newPath = \Includes\Utils\FileManager::getUniquePath(
                    $pathinfo['dirname'],
                    $pathinfo['filename'] . '.' . $newExtension
                );

                $result = rename($path, $newPath);

                if ($result) {
                    $this->path = basename($newPath);
                }
            }
        }

        return $result;
    }

    /**
     * Renew properties by path
     *
     * @param string $path Path
     *
     * @return boolean
     */
    protected function renewByPath($path)
    {
        $result = parent::renewByPath($path);

        if ($result) {
            $data = @getimagesize($path);

            if (is_array($data)) {
                $this->setWidth($data[0]);
                $this->setHeight($data[1]);
                $this->setMime($data['mime']);
                $hash = \Includes\Utils\FileManager::getHash($path);
                if ($hash) {
                    $this->setHash($hash);
                }

            } else {
                $result = false;
            }
        }

        return $result;
    }

    // {{{ Resized icons

    /**
     * Get resized image URL
     *
     * @param integer $width  Width limit OPTIONAL
     * @param integer $height Height limit OPTIONAL
     *
     * @return array (new width + new height + URL)
     */
    public function getResizedURL($width = null, $height = null)
    {
        if ($this->isUseDynamicImageResizing()) {
            $result = $this->doResize($width, $height, false);

        } else {
            $name = $this->isURL()
                ? pathinfo($this->getPath(), \PATHINFO_FILENAME) . '.' . $this->getExtension()
                : $this->getPath();

            $size = ($width ?: 'x') . '.' . ($height ?: 'x');
            $path = $this->getResizedPath($size, $name);

            list($newWidth, $newHeight) = \XLite\Core\ImageOperator::getCroppedDimensions(
                $this->getWidth(),
                $this->getHeight(),
                $width,
                $height
            );

            $url = $this->isResizedIconAvailable($path)
                ? $this->getResizedPublicURL($size, $name)
                : $this->getUrl();

            $result = array($newWidth, $newHeight, $url);
        }


        return $result;
    }

    /**
     * Resize images
     *
     * @return void
     */
    public function prepareSizes()
    {
        $this->doResizeAll(\XLite\Logic\ImageResize\Generator::getModelImageSizes(get_class($this)));
    }

    /**
     * Do resize
     *
     * @param array   $sizes     Sizes
     * @param boolean $doRewrite Rewrite flag OPTIONAL
     *
     * @return boolean
     */
    public function doResizeAll($sizes, $doRewrite = false)
    {
        $result = array();

        foreach ($sizes as $size) {
            list($width, $height) = $size;

            $result[] = $this->doResize($width, $height, $doRewrite);
        }

        return $result;
    }

    /**
     * Do resize
     *
     * @param integer $width     Width limit OPTIONAL
     * @param integer $height    Height limit OPTIONAL
     * @param boolean $doRewrite Rewrite flag OPTIONAL
     *
     * @return array
     */
    public function doResize($width = null, $height = null, $doRewrite = false)
    {
        $name = $this->isURL()
            ? pathinfo($this->getPath(), \PATHINFO_FILENAME) . '.' . $this->getExtension()
            : $this->getPath();

        $size = ($width ?: 'x') . '.' . ($height ?: 'x');
        $path = $this->getResizedPath($size, $name);

        list($newWidth, $newHeight) = \XLite\Core\ImageOperator::getCroppedDimensions(
            $this->getWidth(),
            $this->getHeight(),
            $width,
            $height
        );

        $url = $this->getResizedPublicURL($size, $name);

        if (!$this->isResizedIconAvailable($path)
            || $doRewrite
        ) {
            $result = $this->resizeIcon($newWidth, $newHeight, $path);
            if (!$result) {
                $url = $this->getURL();
            }

        } elseif (!$this->isResizedIconAvailable($path)) {
            $url = $this->getURL();
        }

        return array($newWidth, $newHeight, $url);
    }

    /**
     * Get resized file system path
     *
     * @param string $size Size prefix
     * @param string $name File name
     *
     * @return string
     */
    public function getResizedPath($size, $name)
    {
        return $this->getRepository()->getFileSystemCacheRoot($size) . $name;
    }

    /**
     * Get resized file public URL
     *
     * @param string $size Size prefix
     * @param string $name File name
     *
     * @return string
     */
    protected function getResizedPublicURL($size, $name)
    {
        $name = implode(LC_DS, array_map('rawurlencode', explode(LC_DS, $name)));

        return \XLite::getInstance()->getShopURL(
            $this->getRepository()->getWebCacheRoot($size) . '/' . $name,
            \XLite\Core\Request::getInstance()->isHTTPS()
        );
    }

    /**
     * Check - resized icon is available or not
     *
     * @param string $path Resized image path
     *
     * @return boolean
     */
    protected function isResizedIconAvailable($path)
    {
        return \Includes\Utils\FileManager::isFile($path) && $this->getDate() <= filemtime($path);
    }

    /**
     * Resize icon
     *
     * @param integer $width  Destination width
     * @param integer $height Destination height
     * @param string  $path   Write path
     *
     * @return array
     */
    protected function resizeIcon($width, $height, $path)
    {
        $operator = new \XLite\Core\ImageOperator($this);
        list($newWidth, $newHeight, $result) = $operator->resizeDown($width, $height);

        return false !== $result && \Includes\Utils\FileManager::write($path, $operator->getImage())
            ? array($newWidth, $newHeight)
            : null;
    }

    /**
     * Resize on view
     *
     * @return boolean
     */
    protected function isUseDynamicImageResizing()
    {
        return \Xlite\Core\Config::getInstance()->Performance->use_dynamic_image_resizing;
    }

    // }}}

    /**
     * Set width
     *
     * @param integer $width
     * @return Vendor
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Vendor
     */
    public function setHeight($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return Vendor
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Get hash
     *
     * @return string 
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * Set needProcess
     *
     * @param boolean $needProcess
     * @return Vendor
     */
    public function setNeedProcess($needProcess)
    {
        $this->needProcess = $needProcess;
        return $this;
    }

    /**
     * Get needProcess
     *
     * @return boolean 
     */
    public function getNeedProcess()
    {
        return $this->needProcess;
    }
}
