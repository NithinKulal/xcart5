<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\View\Model\Shipping;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\ShippingMethod;

abstract class Offline extends \XLite\View\Model\Shipping\Offline implements \XLite\Base\IDecorator
{
    protected function postprocessSuccessActionCreate()
    {
        parent::postprocessSuccessActionCreate();

        Mediator::getInstance()->addMessage(
            new ShippingMethod('Add Shipping Method', $this->getModelObject())
        );
    }
}
