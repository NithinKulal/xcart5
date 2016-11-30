<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Product\Details\Customer;

/**
 * Mobile Gallery
 *
 * @ListChild (list="product.details.page.image", weight="11")
 */
class GalleryMobile extends \XLite\View\Product\Details\Customer\Gallery
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/parts/gallery_mobile.twig';
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        if (static::isDisplayRequired(['product']) && !$this->isVisibleAsHidden()) {
            $list[] = array(
                'file'  => $this->getDir() . '/parts/gallery_visible.less',
                'media' => 'screen',
                'merge' => 'bootstrap/css/bootstrap.less',
            );
        }

        return $list;
    }

    /**
     * Returns the minimal count of product images to trigger slider mode
     *
     * @return integer
     */
    protected function getMinCountForSlider()
    {
        return 3;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getCSSClasses()
    {
        return ' mobile';
    }
}
