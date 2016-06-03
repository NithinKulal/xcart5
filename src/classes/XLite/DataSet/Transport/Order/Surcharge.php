<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\DataSet\Transport\Order;

/**
 * Surcharge info
 */
class Surcharge extends \XLite\DataSet\Transport\ATransport
{
    /**
     * Define keys
     *
     * @return array
     */
    protected function defineKeys()
    {
        return array(
            'name',
            'notAvailableReason',
        );
    }
}
