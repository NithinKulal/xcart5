<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Model\Shipping;

use XLite\Module\XC\Concierge\Core\Mediator;
use XLite\Module\XC\Concierge\Core\Track\ShippingMethod;

/**
 * Payment method
 */
abstract class Method extends \XLite\Model\Shipping\Method implements \XLite\Base\IDecorator
{
    /**
     * @param boolean $value
     */
    public function setAdded($value)
    {
        $changed = $this->getAdded() !== (bool) $value;

        parent::setAdded($value);

        if ($this->isPersistent() && $changed && ($this->getModuleName() || 'offline' === $this->getProcessor())) {
            Mediator::getInstance()->addMessage(
                new ShippingMethod(
                    $value ? 'Add Shipping Method' : 'Remove Shipping Method',
                    $this
                )
            );
        }
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return Method
     */
    public function setEnabled($enabled)
    {
        $changed = $this->getEnabled() !== (bool) $enabled;

        parent::setEnabled($enabled);

        if ($this->isPersistent() && $changed && $this->getAdded() && ($this->getModuleName() || 'offline' === $this->getProcessor())) {
            Mediator::getInstance()->addMessage(
                new ShippingMethod(
                    $enabled ? 'Enable Shipping Method' : 'Disable Shipping Method',
                    $this
                )
            );
        }
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        $result = $this->moduleName;

        if (!$this->isFromMarketplace()) {
            $processor = $this->getProcessorObject();
            if ($processor && $processor->getModule()) {
                $result = parent::getModuleName();
            }
        }

        return $result;
    }
}
