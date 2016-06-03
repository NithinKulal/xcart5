<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ImageOperator;

/**
 * Default operator
 */
class DefaultOperator extends \XLite\Core\ImageOperator\AImageOperator
{
    /**
     * Image file store
     *
     * @var string
     */
    protected $image;

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

        $this->image = $image;
    }

    /**
     * Get image content
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image
            ? $this->image->getBody()
            : null;
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
        return false;
    }
}
