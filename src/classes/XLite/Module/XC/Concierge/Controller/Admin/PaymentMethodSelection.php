<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Controller\Admin;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\Track;

abstract class PaymentMethodSelection extends \XLite\Controller\Admin\PaymentMethodSelection implements \XLite\Base\IDecorator
{
    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearchItemsList()
    {
        parent::doActionSearchItemsList();

        Mediator::getInstance()->addMessage(new Track(
            'Payment Method Search',
            [
                'Search Query' => \XLite\Core\Request::getInstance()->substring,
                'Search Country' => \XLite\Core\Request::getInstance()->country,
            ]
        ));
    }
}
