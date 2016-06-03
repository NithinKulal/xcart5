<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model\Payment\Processor;

use \XLite\Module\CDev\Paypal;

/**
 * Paypal Express Checkout payment processor
 */
class PaypalCredit extends Paypal\Model\Payment\Processor\ExpressCheckout
{
    /**
     * Referral page URL
     *
     * @var string
     */
    protected $referralPageURL = 'https://www.paypal.com/webapps/mpp/promotional-financing?partner_id=';

    /**
     * Knowledge base page URL
     *
     * @var string
     */
    protected $knowledgeBasePageURL = 'https://www.paypal.com/webapps/mpp/promotional-financing';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $method = Paypal\Main::getPaymentMethod(
            Paypal\Main::PP_METHOD_PC
        );

        $this->api->setMethod($method);
    }

    /**
     * Get payment method row checkout template
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getCheckoutTemplate(\XLite\Model\Payment\Method $method)
    {
        return 'modules/CDev/Paypal/checkout/paypalCredit.twig';
    }

    /**
     * Get the list of merchant countries where this payment processor can work
     *
     * @return array
     */
    public function getAllowedMerchantCountries()
    {
        return array('US');
    }

    /**
     * Get setting value by name
     *
     * @param string $name Name
     *
     * @return mixed
     */
    protected function getSetting($name)
    {
        return $this->getExpressCheckoutMethod()->getSetting($name);
    }

    /**
     * Get express checkout method
     *
     * @return \XLite\Model\Payment\Method
     */
    protected function getExpressCheckoutMethod()
    {
        return Paypal\Main::getPaymentMethod(Paypal\Main::PP_METHOD_EC);
    }
}
