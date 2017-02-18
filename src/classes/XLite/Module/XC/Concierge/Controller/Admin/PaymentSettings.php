<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Controller\Admin;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\PaymentMethod;

abstract class PaymentSettings extends \XLite\Controller\Admin\PaymentSettings implements \XLite\Base\IDecorator
{
    protected function doActionAdd()
    {
        $method = $this->getMethod();
        if (!$method->getAdded()) {
            Mediator::getInstance()->addMessage(new PaymentMethod('Add Payment Method', $method));
        }

        parent::doActionAdd();
    }

    protected function doActionRemove()
    {
        $method = $this->getMethod();
        if ($method->getAdded()) {
            Mediator::getInstance()->addMessage(new PaymentMethod('Remove Payment Method', $method));
        }

        parent::doActionRemove();
    }

    protected function doActionEnable()
    {
        $method = $this->getMethod();
        if ($method && !$method->getEnabled() && $method->canEnable()) {
            Mediator::getInstance()->addMessage(new PaymentMethod('Enable Payment Method', $method));
        }

        parent::doActionEnable();
    }

    protected function doActionDisable()
    {
        $method = $this->getMethod();
        if ($method && $method->getEnabled() && !$method->isForcedEnabled()) {
            Mediator::getInstance()->addMessage(new PaymentMethod('Disable Payment Method', $method));
        }

        parent::doActionDisable();
    }

    protected function createOfflinePaymentMethod()
    {
        $method = parent::createOfflinePaymentMethod();
        if ($method) {
            Mediator::getInstance()->addMessage(new PaymentMethod('Add Payment Method', $method));
        }

        return $method;
    }
}
