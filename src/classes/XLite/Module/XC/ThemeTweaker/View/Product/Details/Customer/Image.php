<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ThemeTweaker\View\Product\Details\Customer;

/**
 * Image
 */
abstract class Image extends \XLite\View\Product\Details\Customer\Image implements \XLite\Base\IDecorator
{
    /**
     * Check if the product has any image to ZOOM
     *
     * @return boolean
     */
    protected function hasZoomImage()
    {
        return static::isInPreviewMode() ? false : parent::hasZoomImage();
    }

    /**
     * Enables inline editing mode if current page is a product preview.
     *
     * @return boolean
     */
    public static function isInPreviewMode()
    {
        $controller = \XLite::getController();
        return $controller instanceof \XLite\Controller\Customer\Product
            && $controller->isPreview()
            && !\XLite\Core\Request::getInstance()->isInLayoutMode();
    }
}
