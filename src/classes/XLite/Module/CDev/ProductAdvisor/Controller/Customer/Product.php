<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\Controller\Customer;

/**
 * Product page controller extension
 */
class Product extends \XLite\Controller\Customer\Product implements \XLite\Base\IDecorator
{
    /**
     * Save requested product ID in the recently viewed statistics
     */
    public function handleRequest()
    {
        if (\XLite\Core\Config::getInstance()->CDev->ProductAdvisor->rv_enabled) {
            \XLite\Module\CDev\ProductAdvisor\Main::saveProductIds($this->getProductId());
        }

        parent::handleRequest();
    }
}
