<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator;

/**
 * ImageMagic
 */
class ImageMagic extends \XLite\Core\ImageOperator\AImageOperator
{
    /**
     * Image file store
     *
     * @var string
     */
    protected $image;

    /**
     * Image Magick installation path
     *
     * @var string
     */
    protected $imageMagick = '';

    /**
     * Return Image Magick executable
     *
     * @return string
     */
    public static function getImageMagickExecutable()
    {
        $imageMagickPath = \Includes\Utils\ConfigParser::getOptions(array('images', 'image_magick_path'));

        return !empty($imageMagickPath)
            ? (
                \Includes\Utils\FileManager::findExecutable($imageMagickPath . 'convert')
                ?: \Includes\Utils\FileManager::findExecutable($imageMagickPath . 'magick') // IM v7+
            )
            : '';
    }

    /**
     * Check - enabled or not
     *
     * @return boolean
     */
    public static function isEnabled()
    {
        return parent::isEnabled()
            && (bool) self::getImageMagickExecutable();
    }

    /**
     * Set image
     *
     * @param \XLite\Model\Base\Image $image Image
     *
     * @return void
     */
    public function setImage(\XLite\Model\Base\Image $image)
    {
        parent::setImage($image);

        $this->image = tempnam(LC_DIR_TMP, 'image');

        file_put_contents($this->image, $image->getBody());
    }

    /**
     * Get image content
     *
     * @return string
     */
    public function getImage()
    {
        return file_get_contents($this->image);
    }

    /**
     * Resize procedure
     *
     * @param integer $width  Width
     * @param integer $height Height
     *
     * @return boolean
     */
    public function resize($width, $height)
    {
        $new = tempnam(LC_DIR_TMP, 'image.new');

        $result = $this->execFilmStripLook($new);

        if (0 === $result) {

            $result = $this->execResize($new, $width, $height);

            if (0 === $result) {
                $this->image = $new;
            }
        }

        return 0 === $result;
    }

    /**
     * Execution of preparing film strip look
     *
     * @param string $newImage File path to new image
     *
     * @return integer
     */
    protected function execFilmStripLook($newImage)
    {
        exec(
            '"' . self::getImageMagickExecutable()
                . '" ' . $this->image . ' -coalesce '
                . $newImage,
            $output,
            $result
        );

        return $result;
    }

    /**
     * Execution of resizing
     *
     * @param string  $newImage File path to new image
     * @param integer $width    Width
     * @param integer $height   Height
     *
     * @return integer
     */
    protected function execResize($newImage, $width, $height)
    {
        exec(
            '"' . self::getImageMagickExecutable() . '" '
                . $newImage
                . ' -resize '
                . $width . 'x' . $height
                . ' -quality 100 '
                . $newImage,
            $output,
            $result
        );

        return $result;
    }
}
