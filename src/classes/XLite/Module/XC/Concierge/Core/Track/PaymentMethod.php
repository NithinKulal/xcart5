<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core\Track;

use XLite\Model\Payment\Method;
use XLite\Module\XC\Concierge\Core\ATrack;

class PaymentMethod extends ATrack
{
    /**
     * @var Method
     */
    protected $method;

    /**
     * PaymentMethod constructor.
     *
     * @param string $event
     * @param Method $method
     */
    public function __construct($event, $method)
    {
        $this->event  = $event;
        $this->method = $method;
    }

    /**
     * @return Method
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param Method $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        $method = $this->getMethod();

        return [
            'Payment Name' => $method->getName(),
            'Service Name' => $method->getServiceName(),
        ];
    }
}
