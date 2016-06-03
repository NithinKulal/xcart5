<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\View\ItemsList\Model;

use XLite\Module\XC\Pilibaba;

/**
 * Class represents an order
 */
class OrderTrackingNumber extends \XLite\View\ItemsList\Model\OrderTrackingNumber implements \XLite\Base\IDecorator
{
    /**
     * Quick process
     *
     * @param array $parameters Parameters OPTIONAL
     *
     * @return void
     */
    public function processQuick(array $parameters = array())
    {
        parent::processQuick($parameters);

        if ($this->getOrder()
            && $this->getOrder()->getPaymentMethod()
            && $this->getOrder()->getPaymentMethod()->getProcessor() instanceof Pilibaba\Model\Payment\Processor\Pilibaba
        ) {
            $data = \XLite\Core\Request::getInstance()->getData();
            $new = $data[$this->getCreateDataPrefix()];

            foreach ($new as $id => $value) {
                if (!empty($value['value'])) {
                    $this->getOrder()->getPaymentMethod()->getProcessor()->updateTracking(
                        $this->getOrder()->getPaymentTransactionId(),
                        $value['value']
                    );
                }
            }
        }
    }
}
