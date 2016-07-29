<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Product\Details\Customer;

/**
 * Gallery
 */
class Gallery extends \XLite\View\Product\Details\Customer\Gallery implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'js/details_gallery.js';
        $list[] = 'js/cycle2/jquery.cycle2.min.js';
        $list[] = 'js/cycle2/jquery.cycle2.carousel.min.js';

        return $list;
    }

    /**
     * Returns the minimal count of product images to trigger slider mode
     *
     * @return integer
     */
    protected function getMinCountForSlider()
    {
        return 4;
    }

    /**
     * Checks if slider mode is required for the gallery
     *
     * @return boolean
     */
    protected function isInSliderMode()
    {
        return count($this->getProduct()->getPublicImages()) > $this->getMinCountForSlider();
    }
}
