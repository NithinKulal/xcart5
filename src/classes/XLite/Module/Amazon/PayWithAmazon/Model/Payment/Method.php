<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Model\Payment;

use XLite\Core\Config;
use XLite\Core\Converter;

/**
 * Payment method model
 */
class Method extends \XLite\Model\Payment\Method implements \XLite\Base\IDecorator
{
    /**
     * Get message why we can't switch payment method
     *
     * @return string
     */
    public function getNotSwitchableReason()
    {
        $message   = parent::getNotSwitchableReason();
        $processor = $this->getProcessor();

        if ($processor
            && 'PayWithAmazon' === $this->getServiceName()
        ) {
            if ($this->getSetting('merchant_id')
                && $this->getSetting('client_id')
                && !Config::getInstance()->Security->customer_security
            ) {
                $message = static::t(
                    'The "Pay with Amazon" feature requires https to be properly set up for your store.',
                    [
                        'url' => Converter::buildURL('https_settings'),
                    ]
                );
            } else {
                $message = static::t('The "Pay With Amazon" feature is not configured and cannot be used.');
            }
        }

        return $message;
    }
}
