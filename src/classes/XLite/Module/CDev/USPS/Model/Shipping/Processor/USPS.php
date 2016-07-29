<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\USPS\Model\Shipping\Processor;

/**
 * USPS shipping processor model
 * API documentation: https://www.usps.com/business/webtools-technical-guides.htm
 */
class USPS extends \XLite\Model\Shipping\Processor\AProcessor
{
    /**
     * Types of available API
     */
    const USPS_API_DOMESTIC = 'Domestic';
    const USPS_API_INTL     = 'Intl';

    /**
     * $newMethods is used to prevent duplicating methods in database
     *
     * @var array
     */
    protected $newMethods = array();

    /**
     * Type of API (Domestic | International)
     *
     * @var string
     */
    protected $apiType;

    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'usps';
    }

    /**
     * Returns processor name (displayed name)
     *
     * @return string
     */
    public function getProcessorName()
    {
        return 'U.S.P.S.';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSettingsURL()
    {
        return \XLite\Module\CDev\USPS\Main::getSettingsForm();
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
     * Returns API URL
     *
     * @return string
     */
    public function getApiURL()
    {
        $config = $this->getConfiguration();

        return $config->server_url ?: 'http://testing.shippingapis.com/ShippingAPI.dll';
    }

    // {{{ Rates

    /**
     * Returns shipping rates by shipping order modifier (used on checkout)
     *
     * @param array|\XLite\Logic\Order\Modifier\Shipping $inputData   Shipping order modifier or array of data for request
     * @param boolean                                    $ignoreCache Flag: if true then do not get rates from cache OPTIONAL
     *
     * @return array
     */
    public function getRates($inputData, $ignoreCache = false)
    {
        $rates = parent::getRates($inputData, $ignoreCache);
        if ($rates) {
            $this->setError();
        }

        return $rates;
    }

    /**
     * Returns true as USPS shipping module has not any predefined shipping methods
     *
     * @param string $state Method state flag
     *
     * @return boolean
     */
    protected function hasMethods($state = self::STATE_ENABLED_ONLY)
    {
        return true;
    }

    /**
     * Prepare input data from order modifier
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $inputData Shipping order modifier
     *
     * @return array
     */
    protected function prepareDataFromModifier(\XLite\Logic\Order\Modifier\Shipping $inputData)
    {
        $result = array();

        $sourceAddress = $inputData->getOrder()->getSourceAddress();
        if ('US' === $sourceAddress->getCountryCode()) {
            $result['srcAddress'] = array(
                'zipcode' => $sourceAddress->getZipcode(),
            );
            $result['dstAddress'] = \XLite\Model\Shipping::getInstance()->getDestinationAddress($inputData);
            $result['packages'] = $this->getPackages($inputData);

            // Detect if COD payment method has been selected by customer on checkout
            if ($inputData->getOrder()->getFirstOpenPaymentTransaction()) {
                $paymentMethod = $inputData->getOrder()->getPaymentMethod();

                if ($paymentMethod && 'COD_USPS' === $paymentMethod->getServiceName()) {
                    $result['cod_enabled'] = true;
                }
            }
        }

        return $result;
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
        $result = array();
        $config = $this->getConfiguration();

        if (!empty($inputData['packages']) && isset($inputData['srcAddress'], $inputData['dstAddress'])) {
            $this->setApiType($inputData['dstAddress']);

            $result['USERID'] = $config->userid;
            $result['packages'] = array();
            $result['cod_enabled'] = !empty($inputData['cod_enabled']);
            $result['max_weight'] = 0;

            foreach ($inputData['packages'] as $packKey => $package) {
                $result['max_weight'] = max($result['max_weight'], $package['weight']);
                $result['packages'][] = $this->{'prepareRequestData' . $this->getApiType()}($inputData, $packKey);
            }
        }

        return parent::postProcessInputData($result);
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
        return self::USPS_API_DOMESTIC === $this->getApiType()
            ? $this->performRequestDomestic($data, $ignoreCache)
            : $this->performRequestIntl($data, $ignoreCache);
    }

    /**
     * Performs domestic request to carrier server and returns array of rates
     *
     * @param array   $data        Array of request parameters
     * @param boolean $ignoreCache Flag: if true then do not get rates from cache
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function performRequestDomestic($data, $ignoreCache)
    {
        $rates = array();

        // Get services to calculate rates
        $services = $this->isStaticCODPrice() ? static::getAllServices() : static::getServices();

        foreach ($services as $code => $serviceData) {
            if (!empty($serviceData['maxWeight']) && $serviceData['maxWeight'] < $data['max_weight']) {
                continue;
            }

            $requestServices = array($code);

            if (!empty($serviceData['subServices'])) {
                foreach ($serviceData['subServices'] as $ssCode) {
                    $requestServices[] = $code . ' ' . $ssCode;
                }
            }

            foreach ($requestServices as $serviceCode) {
                $this->setError();

                $data['serviceCode'] = $serviceCode;
                $rates[] = $this->doQuery($data, $ignoreCache);
            }
        }

        return call_user_func_array('array_merge', $rates);
    }

    /**
     * Performs international request to carrier server and returns array of rates
     *
     * @param array   $data        Array of request parameters
     * @param boolean $ignoreCache Flag: if true then do not get rates from cache
     *
     * @return \XLite\Model\Shipping\Rate[]
     */
    protected function performRequestIntl($data, $ignoreCache)
    {
        return $this->doQuery($data, $ignoreCache);
    }

    // }}}

    /**
     * Performs request to USPS server and returns array of rates
     *
     * @param array   $data        Array of request parameters
     * @param boolean $ignoreCache Flag: if true then do not get rates from cache
     *
     * @return array
     */
    protected function doQuery($data, $ignoreCache)
    {
        $result = null;
        $rates = array();
        $config = $this->getConfiguration();

        $xmlData = $this->getXMLData($data);

        $currencyRate = (float) $config->currency_rate;
        $currencyRate = (0 < $currencyRate ? $currencyRate : 1);

        $postURL = $this->getApiURL() . '?API=' . $this->getApiName() . '&XML=' . urlencode(preg_replace('/>(\s+)</', '><', $xmlData));

        try {
            if (!$ignoreCache) {
                $cachedRate = $this->getDataFromCache($postURL);
            }

            if (isset($cachedRate)) {
                // Get rates from cache
                $result = $cachedRate;

            } elseif (\XLite\Model\Shipping::isIgnoreLongCalculations()) {
                // Ignore rates calculation
                return array();

            } else {
                // Calculate rate
                $bouncer  = new \XLite\Core\HTTP\Request($postURL);
                $bouncer->requestTimeout = 5;
                $response = $bouncer->sendRequest();

                if ($response && 200 == $response->code) {
                    $result = $response->body;
                    $this->saveDataInCache($postURL, $result);

                    if ($config->debug_enabled) {
                        \XLite\Logger::logCustom(
                            'USPS',
                            var_export(
                                array(
                                    'Request URL' => $postURL,
                                    'Request XML' => $xmlData,
                                    'Response'    => \XLite\Core\XML::getInstance()->getFormattedXML($result),
                                ),
                                true
                            )
                        );
                    }

                } else {
                    $this->setError(sprintf('Error while connecting to the USPS host (%s)', $this->getApiURL()));
                }
            }

            $response = !$this->hasError()
                ? $this->parseResponse($result)
                : array();

            $this->apiCommunicationLog[] = array(
                'request'  => $postURL,
                'xml'      => htmlentities(preg_replace('/(USERID=")([^"]+)/', '\1***', $xmlData)),
                'response' => htmlentities(\XLite\Core\XML::getInstance()->getFormattedXML($result)),
            );

            if (!$this->hasError() && !isset($response['err_msg']) && !empty($response['postage'])) {
                foreach ($response['postage'] as $postage) {
                    $rate = new \XLite\Model\Shipping\Rate();

                    $method = $this->getMethodByCode($postage['CLASSID'], static::STATE_ALL);

                    if (null === $method) {
                        // Unknown method received: add this to the database with disabled status
                        $method = $this->createMethod(
                            $postage['CLASSID'],
                            $postage['MailService'],
                            $config->autoenable_new_methods
                        );
                    }

                    if ($method && $method->getEnabled()) {
                        // Method is registered and enabled

                        $rate->setMethod($method);

                        $codPrice = 0;

                        $rateValue = (float) $postage['Rate'];

                        if (!$this->isStaticCODPrice() && isset($postage['SpecialServices'])) {
                            if (isset($postage['SpecialServices'][6])
                                && 'true' === $postage['SpecialServices'][6]['Available']
                            ) {
                                // Shipping service supports COD
                                $extraData = new \XLite\Core\CommonCell();
                                $extraData->cod_supported = true;
                                $extraData->cod_rate = ($rateValue + ((float) $postage['SpecialServices'][6]['Price'])) * $currencyRate;
                                $rate->setExtraData($extraData);

                                if ($data['cod_enabled']) {
                                    // Calculate COD fee if COD payment method is selected
                                    $codPrice = (float) $postage['SpecialServices'][6]['Price'];
                                }
                            }

                        } elseif ($this->isStaticCODPrice() && $this->isMethodSupportCOD($method)) {
                            $codStaticPrice = (float) $config->cod_price;

                            if (0 < $codStaticPrice) {
                                // Shipping service supports COD
                                $extraData = new \XLite\Core\CommonCell();
                                $extraData->cod_supported = true;
                                $extraData->cod_rate = ($rateValue + $codStaticPrice) * $currencyRate;
                                $rate->setExtraData($extraData);

                                if ($data['cod_enabled']) {
                                    // Calculate COD fee if COD payment method is selected
                                    $codPrice = $codStaticPrice;
                                }
                            }
                        }

                        // Base rate is a sum of base rate and COD fee
                        $rate->setBaseRate(($rateValue + $codPrice) * $currencyRate);

                        if (isset($rates[$postage['MailService']])) {
                            // Multipackaging: sum base rate and COD fee for each rated packages

                            $rates[$postage['MailService']]->setBaseRate(
                                $rates[$postage['MailService']]->getBaseRate() + $rate->getBaseRate()
                            );

                            if ($rate->getExtraData()->cod_rate) {
                                $extra = $rates[$postage['MailService']]->getExtraData();
                                $extra->cod_rate = $extra->cod_rate + $rate->getExtraData()->cod_rate;
                                $rates[$postage['MailService']]->setExtraData($extra);
                            }
                        } else {
                            $rates[$postage['MailService']] = $rate;
                        }
                    }
                }
            } elseif (!$this->hasError()) {
                $this->setError(isset($response['err_msg']) ? $response['err_msg'] : 'Unknown error');
            }

        } catch (\Exception $e) {
            $this->setError('Exception: ' . $e->getMessage());
        }

        return $rates;
    }

    /**
     * Parses response for current type of API and returns an associative array
     *
     * @param string $data Response received from USPS
     *
     * @return array
     */
    protected function parseResponse($data)
    {
        return $this->{'parseResponse' . $this->getApiType()}($data);
    }

    /**
     * Returns XML-formatted request string for current type of API
     *
     * @param array $data Array of request values
     *
     * @return string
     */
    protected function getXMLData($data)
    {
        return $this->{'getXMLData' . $this->getApiType()}($data);
    }

    // }}}

    // {{{ Domestic API specific methods

    /**
     * Returns array of data for package (RateV4 request)
     *
     * @param array  $data    Array of input data
     * @param string $packKey Key of current package
     *
     * @return array
     */
    protected function prepareRequestDataDomestic($data, $packKey)
    {
        list($pounds, $ounces) = $this->getPoundsOunces($data['packages'][$packKey]['weight']);

        $config = $this->getConfiguration();
        $dim = isset($data['packages'][$packKey]['box'])
            ? $data['packages'][$packKey]['box']
            : $data['packages'][$packKey];

        $result = array(
            'ZipOrigination' => $this->sanitizeZipcode($data['srcAddress']['zipcode']), // lenght=5, pattern=/\d{5}/
            'ZipDestination' => $this->sanitizeZipcode($data['dstAddress']['zipcode']), // lenght=5, pattern=/\d{5}/
            'Pounds' => (int) $pounds, // integer, range=0-70
            'Ounces' => sprintf('%.1f', $ounces), // decimal, range=0.0-1120.0, totalDigits=10
            'Container' => $config->container,  // RECTANGULAR | NONRECTANGULAR | ...
            'Size' => $config->package_size,  // REGULAR | LARGE
            'FirstClassMailType' => $config->first_class_mail_type, // LETTER | PARCEL | FLAT | POSTCARD | PACKAGE SERVICE
            'Width' => sprintf('%.1f', $dim['width']), // Units=inches, decimal, min=0.0, totalDigits=10. Required for LARGE
            'Length' => sprintf('%.1f', $dim['length']), // Units=inches, decimal, min=0.0, totalDigits=10. Required for LARGE
            'Height' => sprintf('%.1f', $dim['height']), // Units=inches, decimal, min=0.0, totalDigits=10. Required for LARGE
            'Girth' => sprintf('%.1f', $config->girth), // Units=inches, decimal, min=0.0, totalDigits=10. Required for size=LARGE and container=NONRECTANGULAR | VARIABLE/NULL
            'Value' => sprintf('%.2f', $data['packages'][$packKey]['subtotal']), // decimal, min=0.00, totalDigits=10
            'Machinable' => $config->machinable ? 'true' : 'false',
            'AmountToCollect' => sprintf('%.2f', $data['packages'][$packKey]['subtotal']),
        );

        return $result;
    }

    /**
     * Returns XML-formatted string for RateV4 request
     *
     * @param array $data Array of request values
     *
     * @return string
     */
    protected function getXMLDataDomestic($data)
    {
        $packId = 0;

        $packagesXML = '';

        foreach ($data['packages'] as $pack) {
            $packId++;

            $packIdStr = sprintf('%02d', $packId);

            if (!empty($pack['Girth'])
                && 0 < (float) $pack['Girth']
                && in_array($pack['Container'], array('NONRECTANGULAR', 'VARIABLE'), true)
            ) {
                $girth = <<<OUT
        <Girth>{$pack['Girth']}</Girth>
OUT;
            } else {
                $girth = '';
            }

            $amountToCollectXML = <<<OUT
        <AmountToCollect>{$pack['AmountToCollect']}</AmountToCollect>
OUT;

            if (preg_match('/FIRST CLASS/', $data['serviceCode'])) {
                $firstClassMailTypeXML = <<<OUT
        <FirstClassMailType>{$pack['FirstClassMailType']}</FirstClassMailType>
OUT;

            } else {
                $firstClassMailTypeXML = '';
            }

            $packagesXML .= <<<OUT
    <Package ID="{$packIdStr}">
        <Service>{$data['serviceCode']}</Service>
$firstClassMailTypeXML
        <ZipOrigination>{$pack['ZipOrigination']}</ZipOrigination>
        <ZipDestination>{$pack['ZipDestination']}</ZipDestination>
        <Pounds>{$pack['Pounds']}</Pounds>
        <Ounces>{$pack['Ounces']}</Ounces>
        <Container>{$pack['Container']}</Container>
        <Size>{$pack['Size']}</Size>
        <Width>{$pack['Width']}</Width>
        <Length>{$pack['Length']}</Length>
        <Height>{$pack['Height']}</Height>
$girth
        <Value>{$pack['Value']}</Value>
$amountToCollectXML
        <Machinable>{$pack['Machinable']}</Machinable>
    </Package>
OUT;
        }

        return <<<OUT
<{$this->getApiName()}Request USERID="{$data['USERID']}">
    <Revision>2</Revision>
$packagesXML
</{$this->getApiName()}Request>
OUT;
    }

    /**
     * Parses RateV4 response and returns an associative array
     *
     * @param string $stringData Response received from USPS
     *
     * @return array
     */
    protected function parseResponseDomestic($stringData)
    {
        $result = array();

        $xml = \XLite\Core\XML::getInstance();

        $xmlParsed = $xml->parse($stringData, $err);

        if (isset($xmlParsed['Error'])) {
            $result['err_msg'] = $xml->getArrayByPath($xmlParsed, 'Error/Description/0/#');

        } else {
            $error = $xml->getArrayByPath($xmlParsed, $this->getApiName() . 'Response/Package/Error');

            if ($error) {
                $result['err_msg'] = $xml->getArrayByPath($error, 'Description/0/#');
            }
        }

        if (!isset($result['error_msg'])) {
            $packages = $xml->getArrayByPath($xmlParsed, $this->getApiName() . 'Response/Package');

            if ($packages) {

                $packageRates = array();
                $packagesCount = count($packages);

                foreach ($packages as $i => $package) {
                    $postage = $xml->getArrayByPath($package, '#/Postage');

                    if ($postage) {
                        foreach ($postage as $k => $v) {
                            $serviceName = $this->sanitizeServiceName($xml->getArrayByPath($v, '#/MailService/0/#'));

                            // Get rate types returned in response
                            $rateTypes = array('Rate', 'CommercialRate', 'CommercialPlusRate');
                            $ratePrices = array();

                            foreach ($rateTypes as $rt) {
                                $rateValue = $xml->getArrayByPath($v, '#/' . $rt . '/0/#');
                                if (0 < (float) $rateValue) {
                                    $ratePrices[$rt] = (float) $rateValue;
                                }
                            }

                            // Postage data
                            $postageData = array(
                                'CLASSID' => 'D-' . $xml->getArrayByPath($v, '@/CLASSID') . '-' . static::getUniqueMethodID($serviceName),
                                'MailService' => $this->getUSPSNamePrefix() . $serviceName,
                                'Rate' => $this->getRatePrice($ratePrices),
                            );

                            $specialServices = $xml->getArrayByPath($v, '#/SpecialServices/0/#');

                            if (isset($specialServices['SpecialService']) && is_array($specialServices['SpecialService'])) {
                                foreach ($specialServices['SpecialService'] as $service) {
                                    $rateServices = array(
                                        'ServiceID'   => $xml->getArrayByPath($service, '#/ServiceID/0/#'),
                                        'ServiceName' => $xml->getArrayByPath($service, '#/ServiceName/0/#'),
                                        'Available'   => $xml->getArrayByPath($service, '#/Available/0/#'),
                                        'Price'       => $xml->getArrayByPath($service, '#/Price/0/#'),
                                    );
                                    $postageData['SpecialServices'][$rateServices['ServiceID']] = $rateServices;
                                }
                            }

                            if (!isset($packageRates[$i])) {
                                $packageRates[$i] = array();
                            }

                            $packageRates[$postageData['CLASSID']][] = $postageData;
                        }

                    } else {
                        $result = array();
                        break;
                    }
                }

                if ($packageRates) {
                    // Get intersection of postages
                    foreach ($packageRates as $packageRatesData) {
                        if (count($packageRatesData) < $packagesCount) {
                            continue;
                        }
                        foreach ($packageRatesData as $v) {
                            $result['postage'][] = $v;
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get rate price
     *
     * @param array $ratePrices Array of available rate prices
     *
     * @return float
     */
    protected function getRatePrice($ratePrices)
    {
        $result = 0;

        $config = $this->getConfiguration();
        $useRateType = $config->use_rate_type;

        if (isset($ratePrices[$useRateType])) {
            $result = $ratePrices[$useRateType];

        } else {
            foreach ($ratePrices as $r) {
                if (0 < $r) {
                    $result = $r;
                    break;
                }
            }
        }

        return $result;
    }

    // }}} Domestic API specific methods

    // {{{ International API specific methods

    /**
     * Returns array of data for package (IntlRateV2 request)
     *
     * @param array  $data    Array of input data
     * @param string $packKey Key of current package
     *
     * @return array
     */
    protected function prepareRequestDataIntl($data, $packKey)
    {
        list($pounds, $ounces) = $this->getPoundsOunces($data['packages'][$packKey]['weight']);

        $config = $this->getConfiguration();
        list($length, $width, $height) = $config->dimensions;
        $dim = isset($data['packages'][$packKey]['box'])
            ? $data['packages'][$packKey]['box']
            : $data['packages'][$packKey];


        $result = array(
            'Pounds' => (int) $pounds, // integer, range=0-70
            'Ounces' => sprintf('%.1f', $ounces), // decimal, range=0.0-1120.0, totalDigits=10
            'Machinable' => $config->machinable ? 'true' : 'false',
            'MailType' => $config->mail_type,  // Package | Postcards or aerogrammes | Envelope | LargeEnvelope | FlatRate
            'ValueOfContents' => sprintf('%.2f', $data['packages'][$packKey]['subtotal']), // decimal
            'Country' => $this->getUSPSCountryByCode($data['dstAddress']['country']), // lenght=5, pattern=/\d{5}/
            'Container' => $config->container_intl,  // RECTANGULAR | NONRECTANGULAR
            'Size' => $config->package_size,  // REGULAR | LARGE
            'Width' => sprintf('%.1f', $dim['width']), // Units=inches, decimal, min=0.0, totalDigits=10. Required for LARGE
            'Length' => sprintf('%.1f', $dim['length']), // Units=inches, decimal, min=0.0, totalDigits=10. Required for LARGE
            'Height' => sprintf('%.1f', $dim['height']), // Units=inches, decimal, min=0.0, totalDigits=10. Required for LARGE
            'Girth' => sprintf('%.1f', $config->girth), // Units=inches, decimal, min=0.0, totalDigits=10. Required for size=LARGE and container=NONRECTANGULAR | VARIABLE/NULL
            'GXG' => $config->gxg,
            'GXGPOBoxFlag' => $config->gxg_pobox ? 'Y' : 'N',
            'GXGGiftFlag' => $config->gxg_gift ? 'Y' : 'N',
            'OriginZip' => $this->sanitizeZipcode($data['srcAddress']['zipcode']), // length=5, pattern=/\d{5}/
            'CommercialFlag' => $config->commercial ? 'Y' : 'N', // Y | N
            'ExtraServices' => array(),
        );

        return $result;
    }

    /**
     * Returns XML-formatted string for IntlRateV2 request
     *
     * @param array $data Array of request values
     *
     * @return string
     */
    protected function getXMLDataIntl($data)
    {
        $packId = 0;
        $packages = '';

        foreach ($data['packages'] as $pack) {
            $packId++;

            $packIdStr = sprintf('%02d', $packId);

            if ($pack['GXG']) {
                $gxg = <<<OUT
        <GXG>
            <POBoxFlag>{$pack['GXGPOBoxFlag']}</POBoxFlag>
            <GiftFlag>{$pack['GXGGiftFlag']}</GiftFlag>
        </GXG>
OUT;
            } else {
                $gxg = '';
            }

            $packages .= <<<OUT
    <Package ID="{$packIdStr}">
        <Pounds>{$pack['Pounds']}</Pounds>
        <Ounces>{$pack['Ounces']}</Ounces>
        <Machinable>{$pack['Machinable']}</Machinable>
        <MailType>{$pack['MailType']}</MailType>
$gxg
        <ValueOfContents>{$pack['ValueOfContents']}</ValueOfContents>
        <Country>{$pack['Country']}</Country>
        <Container>{$pack['Container']}</Container>
        <Size>REGULAR</Size>
        <Width>{$pack['Width']}</Width>
        <Length>{$pack['Length']}</Length>
        <Height>{$pack['Height']}</Height>
        <Girth>{$pack['Girth']}</Girth>
        <OriginZip>{$pack['OriginZip']}</OriginZip>
        <CommercialFlag>{$pack['CommercialFlag']}</CommercialFlag>
    </Package>
OUT;
        }

        return <<<OUT
<{$this->getApiName()}Request USERID="{$data['USERID']}">
    <Revision>2</Revision>
$packages
</{$this->getApiName()}Request>
OUT;
    }

    /**
     * Parses IntlRateV2 response and returns an associative array
     *
     * @param string $stringData Response received from USPS
     *
     * @return array
     */
    protected function parseResponseIntl($stringData)
    {
        $result = array();

        $xml = \XLite\Core\XML::getInstance();

        $xmlParsed = $xml->parse($stringData, $err);

        if (isset($xmlParsed['Error'])) {
            $result['err_msg'] = $xml->getArrayByPath($xmlParsed, 'Error/Description/0/#');

        } else {
            $error = $xml->getArrayByPath($xmlParsed, $this->getApiName() . 'Response/Package/Error');

            if ($error) {
                $result['err_msg'] = $xml->getArrayByPath($error, 'Description/0/#');
            }
        }

        if (!isset($result['err_msg'])) {
            $postage = $xml->getArrayByPath($xmlParsed, $this->getApiName() . 'Response/Package/Service');

            if ($postage) {
                foreach ($postage as $k => $v) {
                    $serviceName = $this->sanitizeServiceName($xml->getArrayByPath($v, '#/SvcDescription/0/#'));
                    $result['postage'][] = array(
                        'CLASSID' => 'I-' . $xml->getArrayByPath($v, '@/ID') . '-' . static::getUniqueMethodID($serviceName),
                        'MailService' => $this->getUSPSNamePrefix() . $serviceName,
                        'Rate' => $xml->getArrayByPath($v, '#/Postage/0/#'),
                    );
                }
            }
        }

        return $result;
    }

    // }}} International API specific methods

    // {{{ Service methods

    /**
     * Generate unique shipping method ID from its method name
     *
     * @param string $serviceName Name of shipping service (method)
     *
     * @return string
     */
    public static function getUniqueMethodID($serviceName)
    {
        return md5(strtolower(preg_replace('/(&[^;]*;)|\W/', '', strip_tags($serviceName))));
    }

    /**
     * Returns array(pounds, ounces) from a weight value in specific weight units
     *
     * @param float $weight Weight value
     *
     * @return array
     */
    public function getPoundsOunces($weight)
    {
        $pounds = $ounces = 0;

        switch (\XLite\Core\Config::getInstance()->Units->weight_unit) {

            case 'lbs':
                $pounds = $weight;
                break;

            case 'oz':
                $ounces = $weight;
                break;

            default:
                $ounces = \XLite\Core\Converter::convertWeightUnits(
                    $weight,
                    \XLite\Core\Config::getInstance()->Units->weight_unit,
                    'oz'
                );
        }

        if ((int) $pounds < $pounds) {
            $ounces = ($pounds - ((int) $pounds)) * 16;
            $pounds = (int) $pounds;
        }

        return array($pounds, round($ounces, 1));
    }

    /**
     * Returns shipping method name prefix
     *
     * @return string
     */
    public function getUSPSNamePrefix()
    {
        return $this->getProcessorName() . ' ';
    }


    /**
     * Returns a type of API
     *
     * @return string
     */
    protected function getApiType()
    {
        return $this->apiType;
    }

    /**
     * Set a type of API (domestic | intrnational) depending on destination country
     *
     * @param array $address Array of address data
     *
     * @return void
     */
    protected function setApiType($address)
    {
        $this->apiType = ('US' === $address['country'] ? self::USPS_API_DOMESTIC : self::USPS_API_INTL);
    }

    /**
     * Returns the name of API
     *
     * @return string
     */
    protected function getApiName()
    {
        $apiName = array(
            self::USPS_API_DOMESTIC => 'RateV4',
            self::USPS_API_INTL     => 'IntlRateV2',
        );

        return $apiName[$this->getApiType()];
    }

    /**
     * Returns true if USPS module is configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $config = $this->getConfiguration();

        return $config->userid
            && $config->server_url;
    }

    /**
     * Return true if static COD price should be used
     *
     * @return boolean
     */
    protected function isStaticCODPrice()
    {
        $config = $this->getConfiguration();

        return $config->use_cod_price
            || !static::isCODPaymentEnabled();
    }

    /**
     * Return true if shipping method supports COD
     *
     * @param \XLite\Model\Shipping\Method $method Shipping method
     *
     * @return boolean
     */
    protected function isMethodSupportCOD($method)
    {
        return true;
    }

    /**
     * Get list of services to get all rates per one request
     *
     * @return array
     */
    public static function getAllServices()
    {
        return array(
            'ONLINE' => array(
                'name' => 'Online',
            ),
        );
    }

    /**
     * Get services list to get rates individually for each USPS service types
     *
     * @return array
     */
    public static function getServices()
    {
        return array(
            'FIRST CLASS'   => array(
                'name' => 'First Class',
                'subServices' => array(
                    'COMMERCIAL',

                ),
                'maxWeight' => 0.8125, // 13 ounces
            ),
            'PRIORITY'      => array(
                'name' => 'Priority',
                'subServices' => array(
                    'COMMERCIAL',
                    'CPP',
                ),
            ),
            'EXPRESS'       => array(
                'name' => 'Express',
                'subServices' => array(
                    'COMMERCIAL',
                    'CPP',
                    'SH',
                    'SH COMMERCIAL',
                ),
            ),
            'STANDARD POST' => array(
                'name' => 'Standard Post',
            ),
            'MEDIA'         => array(
                'name' => 'Media',
            ),
            'LIBRARY'       => array(
                'name' => 'Library',
            ),
        );
    }

    /**
     * Returns a name of country which is suitable for USPS API
     *
     * @param string $code Country code
     *
     * @return string
     */
    protected function getUSPSCountryByCode($code)
    {
        static $uspsCountries = array(
            'AE' => 'United Arab Emirates',
            'PG' => 'Papua New Guinea',
            'AF' => 'Afghanistan',
            'NZ' => 'New Zealand',
            'FI' => 'Finland',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AI' => 'Anguilla',
            'AG' => 'Antigua and Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AW' => 'Aruba',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaijan',
            'BS' => 'Bahamas',
            'BH' => 'Bahrain',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Belarus',
            'BE' => 'Belgium',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BM' => 'Bermuda',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia-Herzegovina',
            'BW' => 'Botswana',
            'BR' => 'Brazil',
            'VG' => 'British Virgin Islands',
            'BN' => 'Brunei Darussalam',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'MM' => 'Burma',
            'BI' => 'Burundi',
            'KH' => 'Cambodia',
            'CM' => 'Cameroon',
            'CA' => 'Canada',
            'CV' => 'Cape Verde',
            'KY' => 'Cayman Islands',
            'CF' => 'Central African Rep.',
            'TD' => 'Chad',
            'CL' => 'Chile',
            'CN' => 'China',
            'CO' => 'Colombia',
            'KM' => 'Comoros',
            'CG' => 'Congo, Democratic Republic of the',
            'CR' => 'Costa Rica',
            'CI' => 'Cte d\'Ivoire',
            'HR' => 'Croatia',
            'CU' => 'Cuba',
            'CY' => 'Cyprus',
            'CZ' => 'Czech Republic',
            'DK' => 'Denmark',
            'DJ' => 'Djibouti',
            'DM' => 'Dominica',
            'DO' => 'Dominican Republic',
            'EC' => 'Ecuador',
            'EG' => 'Egypt',
            'SV' => 'El Salvador',
            'GQ' => 'Equatorial Guinea',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'ET' => 'Ethiopia',
            'FK' => 'Falkland Islands',
            'FO' => 'Faroe Islands',
            'FJ' => 'Fiji',
            'FR' => 'France',
            'GF' => 'French Guiana',
            'PF' => 'French Polynesia',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia, Republic of',
            'DE' => 'Germany',
            'GH' => 'Ghana',
            'GI' => 'Gibraltar',
            'GB' => 'Great Britain and Northern Ireland',
            'GR' => 'Greece',
            'GL' => 'Greenland',
            'GD' => 'Grenada',
            'GP' => 'Guadeloupe',
            'GU' => 'Guam',
            'GT' => 'Guatemala',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HN' => 'Honduras',
            'HK' => 'Hong Kong',
            'HU' => 'Hungary',
            'IS' => 'Iceland',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'IE' => 'Ireland',
            'IL' => 'Israel',
            'IT' => 'Italy',
            'JM' => 'Jamaica',
            'JP' => 'Japan',
            'JO' => 'Jordan',
            'KZ' => 'Kazakhstan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KP' => 'Korea, Democratic People\'s Republic of',
            'KR' => 'Korea, Republic of (South Korea)',
            'KW' => 'Kuwait',
            'KG' => 'Kyrgyzstan',
            'LA' => 'Laos',
            'LV' => 'Latvia',
            'LB' => 'Lebanon',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libya',
            'LI' => 'Liechtenstein',
            'LT' => 'Lithuania',
            'LU' => 'Luxembourg',
            'MO' => 'Macao',
            'MK' => 'Macedonia',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malaysia',
            'MV' => 'Maldives',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Marshall Islands',
            'MQ' => 'Martinique',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'MX' => 'Mexico',
            'FM' => 'Micronesia, Federated States of',
            'MD' => 'Moldova',
            'MN' => 'Mongolia',
            'MS' => 'Montserrat',
            'MA' => 'Morocco',
            'MZ' => 'Mozambique',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Netherlands',
            'AN' => 'Netherlands Antilles',
            'NC' => 'New Caledonia',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'MP' => 'Northern Mariana Islands, Commonwealth',
            'NO' => 'Norway',
            'OM' => 'Oman',
            'AS' => 'American Samoa',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PA' => 'Panama',
            'PY' => 'Paraguay',
            'PE' => 'Peru',
            'PH' => 'Philippines',
            'PN' => 'Pitcairn Island',
            'PL' => 'Poland',
            'PT' => 'Portugal',
            'PR' => 'Puerto Rico',
            'QA' => 'Qatar',
            'RE' => 'Reunion',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'RW' => 'Rwanda',
            'KN' => 'Saint Christopher (St. Kitts) and Nevis',
            'SH' => 'Saint Helena',
            'LC' => 'Saint Lucia',
            'PM' => 'Saint Pierre and Miquelon',
            'VC' => 'Saint Vincent and the Grenadines',
            'WS' => 'Samoa, American',
            'SM' => 'San Marino',
            'ST' => 'Sao Tome and Principe',
            'SA' => 'Saudi Arabia',
            'SN' => 'Senegal',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovak Republic',
            'SI' => 'Slovenia',
            'SB' => 'Solomon Islands',
            'SO' => 'Somalia',
            'ZA' => 'South Africa',
            'ES' => 'Spain',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SZ' => 'Swaziland',
            'SE' => 'Sweden',
            'CH' => 'Switzerland',
            'SY' => 'Syrian Arab Republic',
            'TW' => 'Taiwan',
            'TJ' => 'Tajikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailand',
            'TG' => 'Togo',
            'TO' => 'Tonga',
            'TT' => 'Trinidad and Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turkey',
            'TM' => 'Turkmenistan',
            'TC' => 'Turks and Caicos Islands',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ukraine',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VA' => 'Vatican City',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
            'VI' => 'Virgin Islands U.S.',
            'WF' => 'Wallis and Futuna Islands',
            'YE' => 'Yemen',
            'YU' => 'Yugoslavia',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
            'CC' => 'Cocos Island',
            'CK' => 'Cook Islands',
            'TP' => 'East Timor',
            'YT' => 'Mayotte',
            'MC' => 'Monaco',
            'NU' => 'Niue',
            'NF' => 'Norfolk Island',
            'TK' => 'Tokelau (Union) Group',
            'UK' => 'United Kingdom',
            'CX' => 'Christmas Island',
            'US' => 'United States',
        );

        return (isset($uspsCountries[$code]) ? $uspsCountries[$code] : null);
    }

    /**
     * Sanitize zipcode value according to USPS requirements, pattern: /\d{5}/
     *
     * @param string $zipcode Zipcode value
     *
     * @return string
     */
    protected function sanitizeZipcode($zipcode)
    {
        return preg_replace('/\D/', '', substr($zipcode, 0, 5));
    }

    /**
     * Sanitize service name returned by USPS
     *
     * @param string $value Service name
     *
     * @return string
     */
    protected function sanitizeServiceName($value)
    {
        $list = get_html_translation_table();

        return strtr($value, array_flip($list));
    }

    // }}}

    // {{{ Package

    /**
     * Get package limits
     *
     * @return array
     */
    protected function getPackageLimits()
    {
        $limits = parent::getPackageLimits();

        $config = $this->getConfiguration();

        $limits['weight'] = $config->max_weight;
        list($limits['length'], $limits['width'], $limits['height']) = $config->dimensions;

        return $limits;
    }

    // }}}

    // {{{ Tracking

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
        return 'https://tools.usps.com/go/TrackConfirmAction.action';
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
        $list['tLabels']   = $trackingNumber;
        $list['tRef']      = 'fullpage';
        $list['tLc']       = 5;
        $list['text28777'] = '';

        return $list;
    }

    // }}}

    // {{{ COD

    /**
     * Check if 'Cash on delivery (USPS)' payment method enabled
     *
     * @return boolean
     */
    public static function isCODPaymentEnabled()
    {
        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(array('service_name' => 'COD_USPS'));

        return $method && $method->getEnabled();
    }

    // }}}
}
