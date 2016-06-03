<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\View\Product\Details\Customer;

/**
 * PhotoBox
 */
class PhotoBox extends \XLite\View\Product\Details\Customer\PhotoBox implements \XLite\Base\IDecorator
{
    /**
     * Check - loupe icon is visible or not
     *
     * @return boolean
     */
    protected function isLoupeVisible()
    {
        return $this->getProduct()->hasImage();
    }
}
