<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\Model\Shipping\Processor;

use XLite\Module\XC\UPS\Model\Shipping;

/**
 * Shipping processor model
 * API: UPS Developer API (XML)
 * API documentation: XCN-1348
 * Shipments supported: Worldwide -> Worldwide
 */
class UPS extends \XLite\Model\Shipping\Processor\AProcessor
{
    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'ups';
    }

    /**
     * Returns processor name
     *
     * @return string
     */
    public function getProcessorName()
    {
        return 'UPS';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSettingsURL()
    {
        return \XLite\Module\XC\UPS\Main::getSettingsForm();
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

    /**
     * Get dimension unit (for UPS configuration page)
     *
     * @return string
     */
    public static function getDimUnit()
    {
        list(, $dUnit) = static::getWeightAndDimUnits();

        return $dUnit;
    }

    /**
     * Returns weight and dimensional units for specified source country code
     *
     * @param string $countryCode Country code
     *
     * @return array
     */
    protected static function getWeightAndDimUnits($countryCode = null)
    {
        $countryCode = $countryCode ?: \XLite\Core\Config::getInstance()->Company->origin_country;

        if (in_array($countryCode, array('DO', 'PR', 'US', 'CA'), true)) {
            $wUnit = 'LBS';
            $dUnit = 'IN';

        } else {
            $wUnit = 'KGS';
            $dUnit = 'CM';
        }

        return array($wUnit, $dUnit);
    }

    /**
     * Returns current configuration
     *
     * @return \XLite\Core\ConfigCell
     */
    public function getConfiguration()
    {
        return parent::getConfiguration();
    }

    /**
     * This method must return the form method 'post' or 'get' value.
     *
     * @param string $trackingNumber
     *
     * @return string
     */
    public function getTrackingInformationMethod($trackingNumber)
    {
        return 'post';
    }

    /**
     * This method must return the URL to the detailed tracking information about the package.
     * Tracking number is provided.
     *
     * @param string $trackingNumber
     *
     * @return null|string
     */
    public function getTrackingInformationURL($trackingNumber)
    {
        return 'http://wwwapps.ups.com/tracking/tracking.cgi';
    }

    /**
     * Defines the form parameters of tracking information form
     *
     * @param string $trackingNumber Tracking number
     *
     * @return array Array of form parameters
     */
    public function getTrackingInformationParams($trackingNumber)
    {
        $list = parent::getTrackingInformationParams($trackingNumber);
        $list['tracknum']                     = $trackingNumber;
        $list['accept_UPS_license_agreement'] = 'yes';
        $list['nonUPS_title']                 = '';
        $list['nonUPS_header']                = '';
        $list['nonUPS_body']                  = '';
        $list['nonUPS_footer']                = '';

        return $list;
    }

    /**
     * Get shipping method admin zone icon URL
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Shipping\Method $method)
    {
        return true;
    }

    /**
     * Disable the possibility to edit the names of shipping methods in the interface of administrator
     *
     * @return boolean
     */
    public function isMethodNamesAdjustable()
    {
        return false;
    }

    /**
     * Get list of address fields required by shipping processor
     *
     * @return array
     */
    public function getRequiredAddressFields()
    {
        return array(
            'country_code',
            'state_id',
            'zipcode',
        );
    }

    // {{{ Rates

    /**
     * Prepare input data from order modifier
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $inputData Shipping order modifier
     *
     * @return array
     */
    protected function prepareDataFromModifier(\XLite\Logic\Order\Modifier\Shipping $inputData)
    {
        $data = array();

        $sourceAddress = $inputData->getOrder()->getSourceAddress();

        $data['srcAddress'] = array(
            'city' => $sourceAddress->getCity(),
            'zipcode' => $sourceAddress->getZipcode(),
            'country' => $sourceAddress->getCountryCode(),
        );

        if ($sourceAddress->getState()) {
            $data['srcAddress']['state'] = $sourceAddress->getState()->getCode();
        }

        $data['dstAddress'] = \XLite\Model\Shipping::getInstance()->getDestinationAddress($inputData);

        if (isset($data['dstAddress']['state'])) {
            $data['dstAddress']['state'] = \XLite\Core\Database::getRepo('XLite\Model\State')->getCodeById(
                $data['dstAddress']['state']
            );
        }

        if (!empty($data['dstAddress'])) {

            // Filter redundant fieilds from destination address (see BUG-3093)
            $allowedDstFields = array('city', 'state', 'country', 'zipcode', 'type');

            foreach ($data['dstAddress'] as $k => $v) {
                if (!in_array($k, $allowedDstFields)) {
                    unset($data['dstAddress'][$k]);
                }
            }
        }

        $data['packages'] = $this->getPackages($inputData);

        $data['cod_enabled'] = false;

        // Detect if COD payment method has been selected by customer on checkout

        if ($inputData->getOrder()->getFirstOpenPaymentTransaction()) {
            $paymentMethod = $inputData->getOrder()->getPaymentMethod();

            if ($paymentMethod && 'COD_UPS' === $paymentMethod->getServiceName()) {
                $data['cod_enabled'] = true;
            }
        }

        return $data;
    }

    /**
     * Post process input data
     *
     * @param array $inputData Prepared input data
     *
     * @return array
     */
    protected function postProcessInputData(array $inputData)
    {
        if (!empty($inputData['packages'])) {
            $inputData['total'] = 0;

            list($wUnit, ) = static::getWeightAndDimUnits($inputData['srcAddress']['country']);

            foreach ($inputData['packages'] as $key => $package) {
                $inputData['packages'][$key]['weight'] = \XLite\Core\Converter::convertWeightUnits(
                    $package['weight'],
                    \XLite\Core\Config::getInstance()->Units->weight_unit,
                    'KGS' === $wUnit ? 'kg' : 'lbs'
                );

                $inputData['packages'][$key]['weight'] = max(0.1, $inputData['packages'][$key]['weight']);

                $inputData['packages'][$key]['subtotal'] = $this->getPackagesSubtotal($package['subtotal']);
                $inputData['total'] += $inputData['packages'][$key]['subtotal'];
            }

        } else {
            $inputData = array();
        }

        return parent::postProcessInputData($inputData);
    }

    /**
     * Get package subtotal with consideration of currency conversion rate
     *
     * @param float $subtotal
     *
     * @return float
     */
    protected function getPackagesSubtotal($subtotal)
    {
        $config = $this->getConfiguration();

        return round($subtotal / ((float) ($config->currency_rate ?: 1)), 2);
    }

    /**
     * Performs request to carrier server and returns array of rates
     *
     * @param array   $data        Array of request parameters
     * @param boolean $ignoreCache Flag: if true then do not get rates from cache
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function performRequest($data, $ignoreCache)
    {
        $rates = array();
        $api = new Shipping\API\UPS($this);

        $cacheKey = static::getCacheKey($data);
        $cachedRate = null;
        if (!$ignoreCache) {
            $cachedRate = $this->getDataFromCache($cacheKey);
        }

        if (null !== $cachedRate) {
            $rates = $cachedRate;

        } elseif (!\XLite\Model\Shipping::isIgnoreLongCalculations()) {
            $rates = $data['cod_enabled'] ? $api->getRatesCOD($data) : $api->getRates($data);

            if ($rates
                && empty($data['cod_enabled'])
                && static::isCODPaymentEnabled()
            ) {
                $ratesCOD = $api->getRatesCOD($data);

                if ($ratesCOD) {
                    foreach ($rates as &$rate) {
                        $rateCode = $rate->getMethod()->getCode();

                        foreach ($ratesCOD as $rateCOD) {
                            if ($rateCOD->getMethod()->getCode() === $rateCode) {
                                $extra = $rate->getExtraData() ?: new \XLite\Core\CommonCell();
                                $extra->cod_supported = true;
                                $extra->cod_rate = $rateCOD->getBaseRate();
                                $rate->setExtraData($extra);
                                break;
                            }
                        }
                    }

                    unset($rate);
                }
            }

            if ($rates) {
                foreach ($rates as $r) {
                    if ($r->getMethod()) {
                        $r->getMethod()->getName();
                    }
                }
            }

            // Save in cache received rates (we should save empty rates to avoid duplicate requests)
            $this->saveDataInCache($cacheKey, $rates ?: array());
        }

        return $rates;
    }

    // }}}

    /**
     * Returns method by code
     *
     * @param string $code  Method code
     * @param string $state Method state flag
     *
     * @return \XLite\Model\Shipping\Method|null
     */
    public function getMethodByCode($code, $state = self::STATE_ENABLED_ONLY)
    {
        return parent::getMethodByCode($code, $state);
    }

    /**
     * Get package limits
     *
     * @return array
     */
    protected function getPackageLimits()
    {
        $limits = parent::getPackageLimits();

        $config = $this->getConfiguration();

        list($limits['length'], $limits['width'], $limits['height']) = $config->dimensions;
        $limits['weight'] = $config->max_weight;

        return $limits;
    }

    /**
     * Returns true if UPS module is configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $config = $this->getConfiguration();

        return $config->accessKey
            && $config->userID
            && $config->password;
    }

    // {{{ Logging

    /**
     * Add message to custom log
     *
     * @param mixed $message Message to log
     */
    public function log($message)
    {
        $config = $this->getConfiguration();

        if ($config->debug_enabled) {
            parent::log($message);
        }
    }

    /**
     * Add api communication message
     *
     * @param string $message API communication log message
     *
     * @return void
     */
    public function addApiCommunicationMessage($message)
    {
        if (!empty($message['request'])) {
            $message['request'] = htmlentities(
                \XLite\Core\XML::getInstance()->getFormattedXML($this->filterRequestData($message['request']))
            );
        }

        if (!empty($message['response'])) {
            $message['response'] = htmlentities(\XLite\Core\XML::getInstance()->getFormattedXML($message['response']));
        }

        parent::addApiCommunicationMessage($message);
    }

    /**
     * Filter request data for logging
     *
     * @param  string $data Request data
     *
     * @return string
     */
    protected function filterRequestData($data)
    {
        return preg_replace(
            array(
                '|<AccessLicenseNumber>.+</AccessLicenseNumber>|i',
                '|<UserId>.+</UserId>|i',
                '|<Password>.+</Password>|i',
                '|<ShipperNumber>.+</ShipperNumber>|i'
            ),
            array(
                '<AccessLicenseNumber>xxx</AccessLicenseNumber>',
                '<UserId>xxx</UserId>',
                '<Password>xxx</Password>',
                '<ShipperNumber>xxx</ShipperNumber>',
            ),
            $data
        );
    }

    // }}}

    // {{{ COD

    /**
     * Check if 'Cash on delivery (UPS)' payment method enabled
     *
     * @return boolean
     */
    public static function isCODPaymentEnabled()
    {
        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(array('service_name' => 'COD_UPS'));

        return $method && $method->getEnabled();
    }

    // }}}

    /**
     * Returns cache key for rates request
     *
     * @param array $inputData
     *
     * @return string
     */
    public function getCacheKey($inputData)
    {
        return md5(serialize($inputData)) . md5(serialize($this->getConfiguration()));
    }
}
