<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

use XLite\Module\CDev\Paypal;

/**
 * Abstract customer
 */
abstract class ACustomer extends \XLite\Controller\Customer\ACustomer implements \XLite\Base\IDecorator
{
    /**
     * Defines the common data for JS
     *
     * @return array
     */
    public function defineCommonJSData()
    {
        $list = parent::defineCommonJSData();

        if (Paypal\Main::isInContextCheckoutAvailable()) {
            $method = Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);
            if ($method && $method->getProcessor()) {

                $list = array_merge(
                    $list,
                    array(
                        'PayPalMerchantId' => Paypal\Main::getMerchantId(),
                        'PayPalEnvironment' => $method->getProcessor()->isTestMode($method) ? 'sandbox' : 'production',
                    )
                );
            }
        }

        return $list;
    }
}
