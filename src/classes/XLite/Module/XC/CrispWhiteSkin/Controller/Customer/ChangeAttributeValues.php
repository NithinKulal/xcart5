<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CrispWhiteSkin\Controller\Customer;

/**
 * Change attribute values from cart / wishlist item
 */
class ChangeAttributeValues extends \XLite\Controller\Customer\ChangeAttributeValues implements \XLite\Base\IDecorator
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getProductTitle()
    {
        return parent::getTitle();
    }
}
