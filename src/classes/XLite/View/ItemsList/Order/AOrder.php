<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\ItemsList\Order;

use XLite\View\ItemsList\AItemsList;

/**
 * AOrder
 */
abstract class AOrder extends AItemsList
{
    /**
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' items-list-orders';
    }

    /**
     * @return string
     */
    protected function getPageBodyDir()
    {
        return 'order';
    }

    /**
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Order';
    }
}
