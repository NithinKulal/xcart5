<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Stripe\View\Checkout;

/**
 * Payment template
 */
abstract class Payment extends \XLite\View\Checkout\Payment implements \XLite\Base\IDecorator
{
    /**
     * Get JS files 
     * 
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(array('service_name' => 'Stripe'));

        if ($method && $method->isEnabled()) {
            $list[] = 'modules/XC/Stripe/payment.js';
            $list[] = array(
                'url' => 'https://checkout.stripe.com/checkout.js',
            );
        }

        return $list;
    }

}
