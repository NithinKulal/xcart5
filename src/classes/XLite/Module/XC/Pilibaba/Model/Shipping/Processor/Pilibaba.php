<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\Model\Shipping\Processor;


/**
 * Shipping processor model
 */
class Pilibaba extends \XLite\Model\Shipping\Processor\AProcessor
{
    const PROCESSOR_ID = 'Pilibaba';

    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return self::PROCESSOR_ID;
    }

    /**
     * getProcessorName
     *
     * @return string
     */
    public function getProcessorName()
    {
        return 'Pilibaba';
    }

    /**
     * Returns activity status
     *
     * @return boolean
     */
    public function isEnabled()
    {
        $controller = \XLite::getInstance()->getController();

        return $controller->getTarget() === 'order'
            || $controller->getAction() === 'start_pilibaba_checkout';
    }

    /**
     * Returns shipping rates
     *
     * @param array|\XLite\Logic\Order\Modifier\Shipping $inputData   Shipping order modifier or array of data for request
     * @param boolean                                    $ignoreCache Flag: if true then do not get rates from cache OPTIONAL
     *
     * @return array
     */
    public function getRates($inputData, $ignoreCache = false)
    {
        $this->errorMsg = null;
        $rates = array();

        if ($this->isConfigured()) {
            $rates = $this->getFixedRates($inputData, $ignoreCache);

        } else {
            $this->errorMsg = 'Pilibaba module is not configured';
        }

        return $rates;
    }

    /**
     * Internal fixed getRates() part
     * 
     * @param array|\XLite\Logic\Order\Modifier\Shipping $inputData   Shipping order modifier or array of data for request
     * @param boolean                                    $ignoreCache Flag: if true then do not get rates from cache OPTIONAL
     *
     * @return array
     */
    protected function getFixedRates($inputData, $ignoreCache = false)
    {
        $rates = array();

        $method = static::getMethod();

        if ($method && $method->getEnabled()) {
            $rate = new \XLite\Model\Shipping\Rate();

            $rate->setMethod($method);
            $rate->setBaseRate(
                $this->getPaymentMethodSetting('shippingFee')
            );

            $rates[] = $rate;
        }

        return $rates;
    }

    protected function getPaymentMethodSetting($name)
    {
        $method = \XLite\Module\XC\Pilibaba\Main::getPaymentMethod();

        return $method->getSetting($name);
    }

    /**
     * Get shipping method for rate
     *
     * @return \XLite\Model\Shipping\Method
     */
    public static function getMethod()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')->findOneBy(
            array(
                'processor' => \XLite\Module\XC\Pilibaba\Model\Shipping\Processor\Pilibaba::PROCESSOR_ID,
            )
        );
    }

    /**
     * Check test mode
     *
     * @return boolean
     */
    public function isTestMode()
    {
        $config = $this->getConfiguration();

        return (bool) $config->test_mode;
    }
}
