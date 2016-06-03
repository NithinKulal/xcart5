<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\Product\Details\Customer;

/**
 * Product image
 */
class Image extends \XLite\View\Product\Details\Customer\Image implements \XLite\Base\IDecorator
{
    /**
     * Define value for hasZoomImage() method
     *
     * @return boolean
     */
    protected function defineHasZoomImage()
    {
        $result = parent::defineHasZoomImage();
        $product = $this->getProduct();

        if (!$result && $product->hasVariants()) {
            foreach ($product->getVariants() as $variant) {
                if ($variant->getImage() && $this->isImageZoomable($variant->getImage())) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get zoom image
     *
     * @return \XLite\Model\Image
     */
    protected function getZoomImage()
    {
        $image = null;

        if ($this->defineHasZoomImage()) {
            foreach ($this->getProduct()->getVariants() as $variant) {
                if ($variant->getImage() && $this->isImageZoomable($variant->getImage())) {
                    $image = $variant->getImage();
                    break;
                }
            }
        }

        return $image;
    }

    /**
     * Get zoom image URL
     *
     * @return string
     */
    protected function getZoomImageURL()
    {
        $image = $this->getZoomImage();

        return $image
            ? $image->getURL()
            : parent::getZoomImageURL();
    }

    /**
     * Get zoom layer width
     *
     * @return integer
     */
    protected function getZoomWidth()
    {
        $image = $this->getZoomImage();

        return $image
            ? min($image->getWidth(), $this->getParam(self::PARAM_ZOOM_MAX_WIDTH))
            : min($this->getProduct()->getImage()->getWidth(), $this->getParam(self::PARAM_ZOOM_MAX_WIDTH));
    }
}
