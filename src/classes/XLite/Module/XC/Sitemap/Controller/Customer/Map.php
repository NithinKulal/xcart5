<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Sitemap\Controller\Customer;

/**
 * Map controller
 */
class Map extends \XLite\Controller\Customer\ACustomer
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Sitemap';
    }

    /**
     * Return the current page location (for the content area)
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->getTitle();
    }
}
