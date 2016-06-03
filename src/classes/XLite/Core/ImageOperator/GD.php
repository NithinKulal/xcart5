<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator;

/**
 * GD
 */
class GD extends \XLite\Core\ImageOperator\AImageOperator
{
    /**
     * Quality
     */
    const QUALITY_JPEG = 90;
    const QUALITY_PNG  = 3;

    /**
     * MIME types
     *
     * @var array
     */
    protected static $types = array(
        'image/jpeg' => 'jpeg',
        'image/jpg'  => 'jpeg',
        'image/gif'  => 'gif',
        'image/xpm'  => 'xpm',
        'image/gd'   => 'gd',
        'image/gd2'  => 'gd2',
        'image/wbmp' => 'wbmp',
        'image/bmp'  => 'wbmp',
        'image/png'  => 'png',
    );

    /**
     * Image resource
     *
     * @var resource
     */
    protected $image;

    /**
     * Check - enabled engine or not
     *
     * @return boolean
     */
    public static function isEnabled()
    {
        return parent::isEnabled()
            && \XLite\Core\Converter::isGDEnabled();
    }

    /**
     * Set image
     *
     * @param \XLite\Model\Base\Image $image Image
     *
     * @return boolean
     */
    public function setImage(\XLite\Model\Base\Image $image)
    {
        $this->image = null;

        $result = parent::setImage($image);

        if ($result && $this->getImageType() && $image->getBody()) {
            $func = 'imagecreatefrom' . $this->getImageType();

            if (function_exists($func)) {
                $data = $image->getBody();

                $fn = tempnam(LC_DIR_TMP, 'image');

                file_put_contents($fn, $data);
                unset($data);

                // $func is assembled from 'imagecreatefrom' + image type
                $this->image = @$func($fn);
                unlink($fn);

                $result = (bool) $this->image;
            }
        }

        return $result;
    }

    /**
     * Get image content
     *
     * @return string
     */
    public function getImage()
    {
        $image = null;

        $func = 'image' . $this->getImageType();

        if ($this->image && function_exists($func)) {
            $quality = $this->getResultQuality();

            ob_start();
            // $func is assembled from 'image' + image type
            if (isset($quality)) {
                $func($this->image, null, $quality);

            } else {
                $func($this->image);
            }

            $image = ob_get_contents();
            ob_end_clean();
        }

        return $image;
    }

    /**
     * Resize
     *
     * @param integer $width  Width
     * @param integer $height Height
     *
     * @return boolean
     */
    public function resize($width, $height)
    {
        $result = false;

        if ($this->image) {
            $newImage = imagecreatetruecolor($width, $height);

            $transparentIndex = imagecolortransparent($this->image);

            if ($transparentIndex >= 0) {
                imagepalettecopy($this->image, $newImage);
                imagefill($newImage, 0, 0, $transparentIndex);
                imagecolortransparent($newImage, $transparentIndex);
                imagetruecolortopalette($newImage, true, 256);

            } else {
                imagealphablending($newImage, false);
                imagesavealpha($newImage, true);

                $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                imagefilledrectangle($newImage, 0, 0, $width, $height, $transparent);
            }

            $result = imagecopyresampled(
                $newImage,
                $this->image,
                0,
                0,
                0,
                0,
                $width,
                $height,
                $this->width,
                $this->height
            );

            if ($result) {
                imagedestroy($this->image);

                $this->image = $newImage;
                $this->width = $width;
                $this->height = $height;

                if (\XLite::getInstance()->getOptions(array('images', 'unsharp_mask_filter_on_resize'))) {
                    include_once LC_DIR_LIB . 'phpunsharpmask.php';

                    $unsharpImage = UnsharpMask($this->image);

                    if ($unsharpImage) {
                        $this->image = $unsharpImage;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get image GD-based type
     *
     * @return string|void
     */
    protected function getImageType()
    {
        return isset(static::$types[$this->getMimeType()]) ? static::$types[$this->getMimeType()] : null;
    }

    /**
     * Returns image quality
     *
     * @return mixed
     */
    protected function getResultQuality()
    {
        switch ($this->getImageType()) {
            case 'jpeg':
                $result = static::QUALITY_JPEG;
                break;

            case 'png':
                $result = static::QUALITY_PNG;
                break;

            default:
                $result = null;
        }

        return $result;
    }
}
