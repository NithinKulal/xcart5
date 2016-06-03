<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

/**
 * Gallery
 *
 * @ListChild (list="product.details.page.image", weight="20")
 */
class Gallery extends \XLite\View\Product\Details\Customer\ACustomer
{
    /**
     * Quicklook list name
     */
    const QUICKLOOK_PAGE = 'product.details.quicklook.image';

    /**
     * Width and height values of the quicklook images
     */
    const QUICKLOOK_IMAGE_WIDTH  = 300;
    const QUICKLOOK_IMAGE_HEIGHT = 300;

    /**
     * Get list of methods, priorities and interfaces for the resources
     *
     * @return array
     */
    protected static function getResourcesSchema()
    {
        return array(
            array('getCommonFiles', 51, \XLite::COMMON_INTERFACE),
            array('getResources',   60, null),
            array('getThemeFiles',  70, null),
        );
    }

    /**
     * Register files from common repository
     *
     * @return array
     */
    public function getCommonFiles()
    {
        $list = parent::getCommonFiles();
        $list['js'][] = 'js/jquery.colorbox-min.js';
        $list['css'][] = 'css/colorbox.css';

        return $list;
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/parts/gallery.css';

        return $list;
    }

    /**
     * Return the max image width depending on whether it is a quicklook popup, or not
     *
     * @return integer
     */
    protected function getWidgetMaxWidth()
    {
        return static::QUICKLOOK_PAGE == $this->viewListName
            ? static::QUICKLOOK_IMAGE_WIDTH
            : \XLite::getController()->getDefaultMaxImageSize(true);
    }

    /**
     * Get product image container max height
     *
     * @return boolean
     */
    protected function getWidgetMaxHeight()
    {
        return static::QUICKLOOK_PAGE == $this->viewListName
            ? static::QUICKLOOK_IMAGE_HEIGHT
            : \XLite::getController()->getDefaultMaxImageSize(false);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/parts/gallery.twig';
    }

    /**
     * Get LightBox library images directory
     *
     * @return string
     */
    protected function getLightBoxImagesDir()
    {
        return \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'images/lightbox',
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL
        );
    }

    /**
     * Check visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && ($this->getProduct()->countImages() > 0);
    }

    /**
     * Check - visible gallery as hidden or not
     *
     * @return boolean
     */
    protected function isVisibleAsHidden()
    {
        return $this->getProduct()->countImages() < 2;
    }

    /**
     * Get list item class attribute
     *
     * @param integer                 $i     Detailed image index
     * @param \XLite\Model\Base\Image $image Image
     *
     * @return string
     */
    protected function getListItemClassAttribute($i, \XLite\Model\Base\Image $image)
    {
        return array(
            'class' => $this->getListItemClass($i, $image),
        );
    }

    /**
     * Get list item class name
     *
     * @param integer                 $i     Detailed image index
     * @param \XLite\Model\Base\Image $image Image
     *
     * @return array
     */
    protected function getListItemClass($i, \XLite\Model\Base\Image $image)
    {
        return 0 == $i ? array('selected') : array();
    }

    /**
     * Get image URL (middle-size)
     *
     * @param \XLite\Model\Base\Image $image  Image
     * @param integer                 $width  Width limit OPTIONAL
     * @param integer                 $height Height limit OPTIONAL
     *
     * @return string
     */
    protected function getMiddleImageURL(\XLite\Model\Base\Image $image, $width = null, $height = null)
    {
        $result = $image->getResizedURL($width, $height);

        return $result[2];
    }
}
