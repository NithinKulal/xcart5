<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

/**
 * Product details stock widget
 */
abstract class Stock extends \XLite\View\Product\Details\Customer\Stock implements \XLite\Base\IDecorator
{
    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->getProduct()->isUpcomingProduct();
    }
}
