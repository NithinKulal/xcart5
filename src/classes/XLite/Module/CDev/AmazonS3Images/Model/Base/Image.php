<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\AmazonS3Images\Model\Base;

/**
 * Image abstract store
 *
 * @MappedSuperclass
 */
abstract class Image extends \XLite\Model\Base\Image implements \XLite\Base\IDecorator
{
    const STORAGE_S3 = '3';

    const IMAGES_NAMESPACE = 'images';

    /**
     * AWS S3 client
     *
     * @var \XLite\Module\CDev\AmazonS3Images\Core\S3
     */
    protected static $s3;

    /**
     * Import running flag
     *
     * @var boolean
     */
    protected static $importRunning = false;

    /**
     * S3 icons cache
     *
     * @var array
     *
     * @Column (type="array", nullable=true)
     */
    protected $s3icons = array();

    /**
     * Flag: Is need migration of image to S3
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $needMigration = false;

    /**
     * Forbid Amazon S3 storage for loading
     *
     * @var boolean
     */
    protected $s3Forbid = false;

    /**
     * Set import running flag
     *
     * @param boolean $flag Flag
     *
     * @return void
     */
    public static function setImportRunning($flag)
    {
        static::$importRunning = $flag;
    }

    /**
     * Set needProcess. Also set needMigration if method called during import routine
     *
     * @param boolean $needProcess
     *
     * @return \XLite\Model\Base\Image
     */
    public function setNeedProcess($needProcess)
    {
        if (static::$importRunning) {
            $this->needMigration = $needProcess;
        }

        return parent::setNeedProcess($needProcess);
    }

    /**
     * Get needMigration flag
     *
     * @return boolean
     */
    public function getNeedMigration()
    {
        return $this->needMigration;
    }

    /**
     * Set needMigration flag
     *
     * @param boolean $value
     *
     * @return \XLite\Model\Base\Image
     */
    public function setNeedMigration($value)
    {
        $this->needMigration = $value;

        return $this;
    }

    /**
     * Get needMigration flag
     *
     * @return boolean
     */
    public function isNeedMigration()
    {
        return $this->getNeedMigration();
    }

    /**
     * Set S3 forbid
     *
     * @param boolean $flag Flag OPTIONAL
     *
     * @return void
     */
    public function setS3Forbid($flag = false)
    {
        $this->s3Forbid = $flag;
    }

    /**
     * Check - S3 is forbid or not
     *
     * @return boolean
     */
    protected function isS3Forbid()
    {
        return $this->s3Forbid || static::$importRunning;
    }

    // {{{ Getters

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        if (static::STORAGE_S3 == $this->getStorageType()) {
            $body = $this->getS3() ? $this->getS3()->read($this->generateS3Path()) : null;

        } else {
            $body = parent::getBody();
        }

        return $body;
    }

    /**
     * Read output
     *
     * @param integer $start  Start popsition OPTIONAL
     * @param integer $length Length OPTIONAL
     *
     * @return boolean
     */
    public function readOutput($start = null, $length = null)
    {
        if (static::STORAGE_S3 == $this->getStorageType()) {
            $result = false;
            $body = $this->getBody();
            if ($body) {
                if (isset($start)) {
                    $body = isset($length) ? substr($body, $start, $length) : substr($body, $start);
                }
                $result = true;
                print ($body);
            }

        } else {
            $result = parent::readOutput($start, $length);
        }

        return $result;
    }

    /**
     * Check if file exists
     *
     * @param string  $path      Path to check OPTIONAL
     * @param boolean $forceFile Flag OPTIONAL
     *
     * @return boolean
     */
    public function isFileExists($path = null, $forceFile = false)
    {
        if (static::STORAGE_S3 == $this->getStorageType() && !$forceFile) {
            $exists = $this->getS3() ? $this->getS3()->isExists($this->generateS3Path($path)) : false;

        } else {
            $exists = parent::isFileExists($path, $forceFile);
        }

        return $exists;
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getURL()
    {
        return static::STORAGE_S3 == $this->getStorageType()
            ? \XLite\Module\CDev\AmazonS3Images\Core\S3::getURL($this->generateS3Path())
            : parent::getURL();
    }

    // }}}

    // {{{ Loading and service

    /**
     * Load from request
     *
     * @param string $key Key in $_FILES service array
     *
     * @return boolean
     */
    public function loadFromRequest($key)
    {
        if (!$this->isS3Forbid() && $this->getS3()) {

            $result = false;
            $path = \Includes\Utils\FileManager::moveUploadedFile($key, LC_DIR_TMP);
            if ($path) {
                $result = $this->loadFromLocalFile($path, $_FILES[$key]['name']);
                \Includes\Utils\FileManager::deleteFile($path);

            } else {
                \XLite\Logger::getInstance()->log('The file was not loaded', LOG_ERR);
            }

        } else {
            $result = parent::loadFromRequest($key);
        }

        return $result;
    }

    /**
     * Load from local file
     *
     * @param string  $path       Absolute path
     * @param string  $basename   File name OPTIONAL
     * @param boolean $makeUnique True - create unique named file
     *
     * @return boolean
     */
    public function loadFromLocalFile($path, $basename = null, $makeUnique = false)
    {
        if (!$this->isS3Forbid() && $this->getS3() && !$this->isTemporaryFile()) {
            $result = false;

            if (\Includes\Utils\FileManager::isExists($path)) {
                $data = @getimagesize($path);
                if (is_array($data)) {
                    $basename = $basename ?: basename($path);
                    $s3Path = $this->generateS3Path($basename);
                    $s3Path = $this->getS3()->generateUniquePath($s3Path);

                    $headers = array(
                        'Content-Type'        => $data['mime'],
                        'Content-Disposition' => 'inline; filename="' . $basename . '"',
                    );

                    if ($this->getS3()->copy($path, $s3Path, $headers)) {
                        $this->setStorageType(static::STORAGE_S3);
                        $this->setMime($data['mime']);

                        if ($this->savePath($s3Path)) {
                            $result = true;
                        }

                    } else {
                        \XLite\Logger::getInstance()->log(
                            '[Amazon S3] The file \'' . $path . '\' was not copied to \'' . $s3Path . '\'',
                            LOG_ERR
                        );
                    }
                }
            }

        } else {
            $result = parent::loadFromLocalFile($path, $basename, $makeUnique);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function loadFromURL($url, $copy2fs = false)
    {
        if ($copy2fs && $this->getS3()) {
            $s3url = \XLite\Module\CDev\AmazonS3Images\Core\S3::getURL('');

            if (preg_match_all('#' . preg_quote($s3url, '#') . '(.+)$#Ss', $url, $matches)) {
                $path = $matches[1][0];
                if (
                    \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance()->isExists($path)
                    && strpos($path, $this->getRepository()->getS3Prefix()) === 0
                ) {
                    $localPath = substr($path, strlen($this->getRepository()->getS3Prefix()));

                    /** @var \XLite\Model\Base\Image $image */
                    if ($image = $this->getRepository()->findOneAmazonS3ImageByPath($localPath)) {
                        $this->setStorageType($image->getStorageType());
                        $this->setMime($image->getMime());
                        $this->setHeight($image->getHeight());
                        $this->setWidth($image->getWidth());
                        $this->setNeedProcess($image->getNeedProcess());
                        $this->setFileName($image->getFileName());
                        $this->setPath($image->getPath());
                        $this->setSize($image->getSize());
                        $this->setHash($image->getHash());

                        return true;
                    } else {
                        $this->setStorageType(static::STORAGE_S3);
                        return $this->savePath($localPath, true);
                    }
                }
            }
        }

        return parent::loadFromURL($url, $copy2fs);
    }

    /**
     * Remove file
     *
     * @param string $path Path OPTIONAL
     *
     * @return void
     */
    public function removeFile($path = null)
    {
        if (static::STORAGE_S3 == $this->getStorageType()) {
            if (
                is_object($this->getS3())
                && !$this->getRepository()->findOneAmazonS3ImageByPath($path ?: $this->getPath(), $this)
            ) {
                $this->getS3()->delete($this->getStoragePath($path));

                if ($this->getId()) {
                    $dir = $this->getStoragePath('icon/' . $this->getId());
                    if ($this->getS3()->isDir($dir)) {
                        $this->getS3()->deleteDirectory($dir);
                    }
                }
                $this->removeResizedImagesFromS3($path);
            }
        } else {
            parent::removeFile($path);
        }
    }

    /**
     * Is allow to remove file
     *
     * @param null $path
     *
     * @return bool
     */
    public function isAllowRemoveFile($path = null)
    {
        $path = $this->getStoragePath($path);

        return !$this->isURL($path)
               && $this->getStorageType() !== static::STORAGE_ABSOLUTE
               && $this->getRepository()->allowRemovePath($path, $this);
    }

    /**
     * Prepare image before remove operation
     *
     * @param string $path Path OPTIONAL
     */
    public function removeResizedImagesFromS3($path = null)
    {
        $path = $path ?: $this->getPath();

        $sizes = $this->getAllSizes();

        $name = $this->isURL()
            ? pathinfo($path, \PATHINFO_FILENAME) . '.' . $this->getExtension()
            : $path;

        foreach ($sizes as $size) {
            list($width, $height) = $size;

            $size = ($width ?: 'x') . '.' . ($height ?: 'x');
            $path = $this->getResizedPath($size, $name);

            if ($this->getS3()->isExists($path)) {
                $isDeleted = $this->getS3()->delete($path);

                if (!$isDeleted) {
                    \XLite\Logger::getInstance()->log('Can\'t delete resized image ' . $path, LOG_DEBUG);
                }
            }
        }
    }

    /**
     * Get storage path
     *
     * @param string $path Path to use OPTIONAL
     *
     * @return string
     */
    public function getStoragePath($path = null)
    {
        if (static::STORAGE_S3 == $this->getStorageType()) {
            $result = $this->generateS3Path($path);

        } else {
            $result = parent::getStoragePath($path);
        }

        return $result;
    }

    /**
     * Return true if current object is TemporaryFile model
     *
     * @return boolean
     */
    protected function isTemporaryFile()
    {
        return $this instanceOf \XLite\Model\TemporaryFile;
    }

    /**
     * Get local path for file-based PHP functions
     *
     * @return array
     */
    protected function getLocalPath()
    {
        if (static::STORAGE_S3 == $this->getStorageType()) {

            $path = tempnam(LC_DIR_TMP, 'analyse_file');
            $result = \Includes\Utils\FileManager::write(
                $path,
                $this->getS3()->read($this->getStoragePath())
            );

            if (!$result) {
                \XLite\Logger::getInstance()->log(
                    'Unable to write data to file \'' . $path . '\'.',
                    LOG_ERR
                );
                $path = false;
            }

            $result = array($path, true);

        } else {
            $result = parent::getLocalPath();
        }

        return $result;
    }

    /**
     * Update file path - change file extension taken from MIME information.
     *
     * @return boolean
     */
    protected function updatePathByMIME()
    {
        return static::STORAGE_S3 == $this->getStorageType() ? true : parent::updatePathByMIME();
    }

    /**
     * Get S3 client
     *
     * @return \XLite\Module\CDev\AmazonS3Images\Core\S3
     */
    protected function getS3()
    {
        if (!isset(static::$s3)) {
            static::$s3 = \XLite\Module\CDev\AmazonS3Images\Core\S3::getInstance();
            if (!static::$s3->isValid()) {
                static::$s3 = false;
            }
        }
        return static::$s3;
    }

    /**
     * Generate AWS S3 short path
     *
     * @param string $path Path from DB OPTIONAL
     *
     * @return string
     */
    protected function generateS3Path($path = null)
    {
        return $this->getRepository()->getS3Prefix() . ($path ?: $this->getPath());
    }

    // }}}

    // {{{ Resize icon

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
        return $this->isUseS3Icons()
            ? $this->generateS3Path($size . '/' . $name)
            : parent::getResizedPath($size, $name);
    }

    /**
     * Get resized image local file system path
     *
     * @param string $size Size prefix
     * @param string $name File name
     *
     * @return string
     */
    public function getLocalResizedPath($size, $name)
    {
        return parent::getResizedPath($size, $name);
    }

    /**
     * Detect size by path and return array(width, height)
     *
     * @param string $path Image path
     *
     * @return array|null
     */
    public function detectSizeByPath($path)
    {
        $result = null;

        if (
            preg_match('/\/(\d+)\.(\d+)\//', $path, $matches)           // New format, e.g. images/product/XXX.YYY/filename.jpg
            || preg_match('/\/(\d+)\.(\d+)\.[a-z]+$/', $path, $matches) // Old format, e.g. images/product/icons/<IMGID>/XXX.YYY.jpg
        ) {
            $result = array(
                $matches[1],
                $matches[2]
            );
        }

        return $result;
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
        return $this->isUseS3Icons()
            ? \XLite\Module\CDev\AmazonS3Images\Core\S3::getURL($this->getResizedPath($size, $name))
            : parent::getResizedPublicURL($size, $name);
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
        $icons = $this->getS3icons();

        return ($this->isUseS3Icons() && $icons)
            ? !empty($icons[$path])
            : parent::isResizedIconAvailable($path);
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
        $result = null;

        if ($this->isUseS3Icons()) {
            $operator = new \XLite\Core\ImageOperator($this);
            list($newWidth, $newHeight, $r) = $operator->resizeDown($width, $height);

            if (false !== $r) {
                $basename = $this->getFileName() ?: basename($this->getPath());
                $headers = array(
                    'Content-Type'        => $this->getMime(),
                    'Content-Disposition' => 'inline; filename="' . $basename . '"',
                );

                if ($this->getS3()->write($path, $operator->getImage(), $headers)) {
                    $icons = $this->getS3icons();
                    $icons[$path] = true;
                    $this->setS3icons($icons);
                    \XLite\Core\Database::getEM()->flush();
                    $result = array($newWidth, $newHeight);
                }
            }

        } else {
            $result = parent::resizeIcon($width, $height, $path);
        }

        return $result;
    }

    /**
     * Use S3 icons
     *
     * @return boolean
     */
    protected function isUseS3Icons()
    {
        return static::STORAGE_S3 == $this->getStorageType();
    }

    // }}}
}
