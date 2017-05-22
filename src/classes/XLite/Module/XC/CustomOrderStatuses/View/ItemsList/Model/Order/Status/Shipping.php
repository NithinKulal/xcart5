<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status;

/**
 * Shipping status items list
 */
class Shipping extends \XLite\Module\XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\AStatus
{
    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName()
    {
        return 'XLite\Model\Order\Status\Shipping';
    }
}