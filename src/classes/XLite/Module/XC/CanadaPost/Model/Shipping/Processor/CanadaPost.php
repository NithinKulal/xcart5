<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Model\Shipping\Processor;

/**
 * Shipping processor model
 * API documentation: https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/rating/default.jsf
 *
 */
class CanadaPost extends \XLite\Model\Shipping\Processor\AProcessor
{
    /**
     * $newMethods is used to prevent duplicating methods in database
     *
     * @var array
     */
    protected $newMethods = array();

    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'capost';
    }

    /**
     * Returns processor name
     *
     * @return string
     */
    public function getProcessorName()
    {
        return 'Canada Post';
    }

    /**
     * Returns settings template
     *
     * @return string
     */
    public function getSettingsTemplate()
    {
        return 'modules/XC/CanadaPost/settings/main.twig';
    }

    /**
     * Returns test template
     *
     * @return string
     */
    public function getTestTemplate()
    {
        return 'modules/XC/CanadaPost/settings/test.twig';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSettingsURL()
    {
        return \XLite\Module\XC\CanadaPost\Main::getSettingsForm();
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
        $commonData = array();

        $sourceAddress = $inputData->getOrder()->getSourceAddress();
        if ('CA' === $sourceAddress->getCountryCode()) {
            $commonData['srcAddress'] = array(
                'zipcode' => $sourceAddress->getZipcode(),
            );
        }

        $commonData['dstAddress'] = \XLite\Model\Shipping::getInstance()->getDestinationAddress($inputData);

        if (!empty($commonData['srcAddress']) && !empty($commonData['dstAddress'])) {
            $data['packages'] = $this->getPackages($inputData);
            $data['commonData'] = $commonData;
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
        $commonData = isset($inputData['commonData']) ? $inputData['commonData'] : array();
        unset($inputData['commonData']);

        if (!empty($inputData['packages'])) {
            foreach ($inputData['packages'] as $key => $package) {
                $package = array_merge($package, $commonData);

                $package['weight'] = \XLite\Core\Converter::convertWeightUnits(
                    $package['weight'],
                    \XLite\Core\Config::getInstance()->Units->weight_unit,
                    'kg'
                );

                \XLite\Module\XC\CanadaPost\Core\API::setCanadaPostConfig($this->getConfiguration());
                $package['subtotal'] = \XLite\Module\XC\CanadaPost\Core\API::applyConversionRate($package['subtotal']);

                $inputData['packages'][$key] = $package;
            }
        } else {
            $inputData = array();
        }

        return parent::postProcessInputData($inputData);
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
        $codeCounter = array();

        foreach ($data['packages'] as $pid => $package) {
            // Perform request for rates for each package
            $packageRates = $this->doQuery($package, $ignoreCache);

            if (!empty($packageRates)) {
                // Assemble package rates to the single rates array

                foreach ($packageRates as $code => $rate) {
                    if (!isset($rates[$code])) {
                        $rates[$code] = $rate;
                        $codeCounter[$code] = 1;

                    } else {
                        $rates[$code]->setBaseRate($rates[$code]->getBaseRate() + $rate->getBaseRate());
                        $codeCounter[$code] ++;
                    }
                }

            } else {
                $rates = array();
                break;
            }
        }

        if ($rates) {
            // Exclude rates for methods which are not available for all packages

            foreach ($codeCounter as $code => $cnt) {
                if (count($data['packages']) !== $cnt) {
                    unset($rates[$code]);
                }
            }
        }

        return $rates;
    }

    // }}}

    /**
     * Returns true if CanadaPost module is configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $config = $this->getConfiguration();

        return $config->user
            && $config->password
            && ($config->customer_number
                || \XLite\Module\XC\CanadaPost\Core\API::QUOTE_TYPE_NON_CONTRACTED
                    === $config->quote_type
            );
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

        // Weight in store weight units
        $limits['weight'] = \XLite\Core\Converter::convertWeightUnits(
            $config->max_weight,
            'kg',
            \XLite\Core\Config::getInstance()->Units->weight_unit
        );

        $limits['length'] = $config->length;
        $limits['width']  = $config->width;
        $limits['height'] = $config->height;

        return $limits;
    }

    /**
     * Low level query
     *
     * @param mixed   $data        Array of prepared package data
     * @param boolean $ignoreCache Flag: if true then do not get rates from cache
     *
     * @return array
     */
    protected function doQuery($data, $ignoreCache)
    {
        $rates = array();

        $config = $this->getConfiguration();
        \XLite\Module\XC\CanadaPost\Core\API::setCanadaPostConfig($config);

        $XMLData = $this->getXMLData($data);

        try {
            $postURL = \XLite\Module\XC\CanadaPost\Core\API::getInstance()->getGetRatesEndpoint();

            if (!$ignoreCache) {
                $cachedRates = $this->getDataFromCache($XMLData);
            }

            if (isset($cachedRates)) {
                $result = $cachedRates;

            } elseif (\XLite\Model\Shipping::isIgnoreLongCalculations()) {
                // Ignore rates calculation
                return array();

            } else {
                $bouncer = new \XLite\Core\HTTP\Request($postURL);
                $bouncer->requestTimeout = 5;
                $bouncer->body = $XMLData;
                $bouncer->verb = 'POST';
                $bouncer->setHeader('Authorization', 'Basic ' . base64_encode($config->user . ':' . $config->password));
                $bouncer->setHeader('Accept', 'application/vnd.cpc.ship.rate-v2+xml');
                $bouncer->setHeader('Content-Type', 'application/vnd.cpc.ship.rate-v2+xml');
                $bouncer->setHeader('Accept-language', \XLite\Module\XC\CanadaPost\Core\API::ACCEPT_LANGUAGE_EN);

                if (\XLite\Module\XC\CanadaPost\Core\API::isOnBehalfOfAMerchant()) {
                    $bouncer->setHeader(
                        'Platform-id',
                        \XLite\Module\XC\CanadaPost\Core\API::getInstance()->getPlatformId()
                    );
                }

                $response = $bouncer->sendRequest();

                $result = $response->body;

                if (200 == $response->code) {
                    $this->saveDataInCache($XMLData, $result);

                } else {
                    $this->setError(sprintf('Error while connecting to the Canada Post host (%s)', $postURL));
                }

                if ($config->debug_enabled) {
                    $this->log(array(
                        'Request URL' => $postURL,
                        'Request XML (Get Rates)' => $XMLData,
                        'Response XML' => \XLite\Core\XML::getInstance()->getFormattedXML($result),
                    ));
                }
            }

            // Save communication log for test request only (ignoreCache is set for test requests only)

            if ($ignoreCache === true) {
                $this->addApiCommunicationMessage(array(
                    'request_url'  => $postURL,
                    'request_data' => $XMLData,
                    'response'     => $result,
                ));
            }

            $response = $this->parseResponse($result);

            if (!$this->hasError() && !isset($response['err_msg']) && !empty($response['services'])) {
                $conversionRate = \XLite\Module\XC\CanadaPost\Core\API::getCurrencyConversionRate();

                foreach ($response['services'] as $service) {
                    $rate = new \XLite\Model\Shipping\Rate();

                    $method = $this->getMethodByCode($service['service_code']);

                    if (null === $method) {
                        // Unknown method received: add this to the database with disabled status
                        $this->createMethod($service['service_code'], $service['service_name'], false);

                    } elseif ($method->getEnabled()) {
                        // Method is registered and enabled

                        $rate->setMethod($method);
                        $rate->setBaseRate($service['rate'] * $conversionRate);

                        $rates[$service['service_code']] = $rate;
                    }
                }

            } elseif (!$this->hasError() || isset($response['err_msg'])) {
                $errorMessage = isset($response['err_msg'])
                    ? $response['err_msg']
                    : ($this->getError() ?: 'Unknown error');

                $this->setError($errorMessage);
            }

        } catch (\Exception $e) {
            $this->setError($e->getMessage());
        }

        return $rates;
    }

    /**
     * parses response and returns an associative array
     *
     * @param string $stringData XML response of capost api
     *
     * @return array
     */
    protected function parseResponse($stringData)
    {
        $result = array();

        $xml = \XLite\Core\XML::getInstance();

        $xmlParsed = $xml->parse($stringData, $err);

        if (isset($xmlParsed['messages'])) {
            $result['err_msg'] = $xml->getArrayByPath($xmlParsed, 'messages/message/description/0/#');
        }

        if (!isset($result['err_msg'])) {
            $services = $xml->getArrayByPath($xmlParsed, 'price-quotes/price-quote');

            if ($services) {
                foreach ($services as $k => $v) {
                    $result['services'][] = array(
                        'service_code' => $xml->getArrayByPath($v, 'service-code/0/#'),
                        'service_name' => $xml->getArrayByPath($v, 'service-name/0/#'),
                        'rate' => $xml->getArrayByPath($v, 'price-details/0/#/due/0/#'),
                    );
                }
            }
        }

        return $result;
    }

    /**
     * Generate XML request
     *
     * @param array $data Array of package data
     *
     * @return string
     */
    protected function getXMLData($data)
    {
        $config = $this->getConfiguration();

        $xmlHeader = '<?xml version="1.0" encoding="utf-8"?'.'>';
        
        //  Option applies to this shipment.
        $opts = array();

        if ($config->coverage > 0
            && $data['subtotal'] > 0
        ) {
            // Add coverage (insuarance) option

            if ($config->coverage != 100) {
                $data['subtotal'] = $data['subtotal'] / 100 * $config->coverage;
            }

            $coverage = \XLite\Module\XC\CanadaPost\Core\API::adjustFloatValue($data['subtotal'], 2, 0.01, 99999.99);

            $opts[] = <<<OUT
    <option>
        <option-code>COV</option-code>
        <option-amount>{$coverage}</option-amount>
    </option>
OUT;
        }

        $optionsXML = '';

        if ($opts) {
            $options = implode(PHP_EOL, $opts);
            $optionsXML = <<<OUT
<options>
$options
</options>
OUT;
        }

        $contractId = '';
        $customerNumber = '';
        if (\XLite\Module\XC\CanadaPost\Core\API::QUOTE_TYPE_CONTRACTED === $config->quote_type) {
            $customerNumber = <<<OUT
<customer-number>{$config->customer_number}</customer-number>
OUT;
            if ($config->contract_id) {
                $contractId = <<<OUT
<contract-id>{$config->contract_id}</contract-id>
OUT;
            }
        }

        $parcelCharacteristics = '';

        $data['weight'] = \XLite\Module\XC\CanadaPost\Core\API::adjustFloatValue($data['weight'], 3, 0.001, 99.999);

        $weight = <<<OUT
<weight>{$data['weight']}</weight>
OUT;

        $dimensions = '';

        if (!empty($data['box'])) {
            $length = $data['box']['length'];
            $width  = $data['box']['width'];
            $height = $data['box']['height'];

        } elseif ($config->length && $config->width && $config->height) {
            $length = $config->length;
            $width  = $config->width;
            $height = $config->height;
        }

        if (!empty($length) && !empty($width) && !empty($height)) {
            $length = \XLite\Module\XC\CanadaPost\Core\API::adjustFloatValue($length, 1, 0.1, 999.9);
            $width  = \XLite\Module\XC\CanadaPost\Core\API::adjustFloatValue($width, 1, 0.1, 999.9);
            $height = \XLite\Module\XC\CanadaPost\Core\API::adjustFloatValue($height, 1, 0.1, 999.9);

            $dimensions =<<<OUT
<dimensions>
    <length>{$length}</length>
    <width>{$width}</width>  
    <height>{$height}</height>
</dimensions>
OUT;
        }
        $parcelCharacteristics .= <<<OUT
<parcel-characteristics>
    {$weight}
    {$dimensions}
</parcel-characteristics>
OUT;

        $dstPostalCode = \XLite\Module\XC\CanadaPost\Core\API::strToUpper(
            preg_replace('/\s+/', '', $data['dstAddress']['zipcode'])
        );

        $srcPostalCode = \XLite\Module\XC\CanadaPost\Core\API::strToUpper(
            preg_replace('/\s+/', '', $data['srcAddress']['zipcode'])
        );

        if ('CA' === $data['dstAddress']['country']) {
            $destination = <<<OUT
<domestic>
    <postal-code>{$dstPostalCode}</postal-code>
</domestic>
OUT;

        } elseif ('US' === $data['dstAddress']['country']) {
            $destination = <<<OUT
<united-states>
    <zip-code>{$dstPostalCode}</zip-code>
</united-states>
OUT;

        } else {
            $destination = <<<OUT
<international>
    <country-code>{$data['dstAddress']['country']}</country-code>
</international>
OUT;
        }
        
        $quoteType = (\XLite\Module\XC\CanadaPost\Core\API::QUOTE_TYPE_CONTRACTED === $config->quote_type)
            ? 'commercial'
            : 'counter';

        $request = <<<OUT
{$xmlHeader}
<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v2">
    {$customerNumber}
    <quote-type>{$quoteType}</quote-type>
    {$optionsXML}
    {$contractId}
    {$parcelCharacteristics}
    <origin-postal-code>{$srcPostalCode}</origin-postal-code>
    <destination>{$destination}</destination>
</mailing-scenario>
OUT;

        return $request;
    }

    /**
     * Add api communication message
     *
     * @param string $message API communication log message
     *
     * @return void
     */
    protected function addApiCommunicationMessage($message)
    {
        if (!empty($message['request_data'])) {
            $message['request_data'] = htmlentities(
                $this->filterRequestData($message['request_data'])
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
     * @param string $data Request data
     *
     * @return string
     */
    protected function filterRequestData($data)
    {
        return preg_replace(
            array(
                '|<customer-number>.+</customer-number>|i',
            ),
            array(
                '<customer-number>xxx</customer-number>',
            ),
            $data
        );
    }
}
