<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\FedEx\Model\Shipping\Processor;

/**
 * FedEx shipping processor model
 * API documentation: FedEx Web Services, Developer Guide 2012, Ver.13 (XCN-1035)
 */
class FEDEX extends \XLite\Model\Shipping\Processor\AProcessor
{
    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'fedex';
    }

    /**
     * Returns processor name (displayed name)
     *
     * @return string
     */
    public function getProcessorName()
    {
        return 'FedEx';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSettingsURL()
    {
        return \XLite\Module\CDev\FedEx\Main::getSettingsForm();
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
            'state_id',
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
        $protocol = 'https://';

        $host = $this->isTestMode()
            ? 'wsbeta.fedex.com:443/web-services'
            : 'ws.fedex.com:443/web-services';

        return $protocol . $host;
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
        $result = array();

        $sourceAddress = $inputData->getOrder()->getSourceAddress();
        $result['srcAddress'] = array(
            'zipcode' => $sourceAddress->getZipcode(),
            'country' => $sourceAddress->getCountryCode(),
        );

        if ($sourceAddress->getState()) {
            $result['srcAddress']['state'] = $sourceAddress->getState()->getCode();
        }

        $result['dstAddress'] = \XLite\Model\Shipping::getInstance()->getDestinationAddress($inputData);

        if (empty($result['dstAddress']['country'])) {
            $result['dstAddress'] = null;

        } elseif (isset($result['dstAddress']['state'])) {
            /** @var \XLite\Model\Repo\State $repo */
            $repo = \XLite\Core\Database::getRepo('XLite\Model\State');
            $result['dstAddress']['state'] = $repo->getCodeById($result['dstAddress']['state']);
        }

        $result['packages'] = $this->getPackages($inputData);

        // Detect if COD payment method has been selected by customer on checkout
        if ($inputData->getOrder()->getFirstOpenPaymentTransaction()) {
            $paymentMethod = $inputData->getOrder()->getPaymentMethod();

            if ($paymentMethod && 'COD_FEDEX' === $paymentMethod->getServiceName()) {
                $result['cod_enabled'] = true;
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

        if (!empty($inputData['packages'])
            && !empty($inputData['srcAddress'])
            && !empty($inputData['dstAddress'])
        ) {
            $result = $inputData;
            $result['packages'] = array();

            foreach ($inputData['packages'] as $packKey => $package) {
                $package['price'] = sprintf('%.2f', $package['subtotal']); // decimal, min=0.00, totalDigits=10
                $package['weight'] = round(
                    \XLite\Core\Converter::convertWeightUnits(
                        $package['weight'],
                        \XLite\Core\Config::getInstance()->Units->weight_unit,
                        'lbs'
                    ),
                    4
                );

                $result['packages'][] = $package;
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
        $rates = array();
        $config = $this->getConfiguration();
        $xmlData = $this->getXMLData($data);

        try {
            if (!$ignoreCache) {
                $cachedRate = $this->getDataFromCache($xmlData);
            }

            $postURL = $this->getApiURL();
            $result = null;

            if (isset($cachedRate)) {
                $result = $cachedRate;

            } elseif (\XLite\Model\Shipping::isIgnoreLongCalculations()) {
                // Ignore rates calculation
                return array();

            } else {
                $bouncer = new \XLite\Core\HTTP\Request($postURL);
                $bouncer->body = $xmlData;
                $bouncer->verb = 'POST';
                $bouncer->requestTimeout = 5;
                $response = $bouncer->sendRequest();

                if (200 == $response->code || !empty($response->body)) {
                    $result = $response->body;
                    if (200 == $response->code) {
                        $this->saveDataInCache($xmlData, $result);
                    }

                    if ($config->debug_enabled) {
                        $this->log(array(
                            'request_url'  => $postURL,
                            'request_data' => $this->filterRequestData($xmlData),
                            'response'     => \XLite\Core\XML::getInstance()->getFormattedXML($result),
                        ));
                    }

                } else {
                    $this->setError(sprintf('Error while connecting to the FedEx host (%s)', $postURL));
                }
            }

            $response = !$this->hasError()
                ? $this->parseResponse($result)
                : array();

            //save communication log for test request only (ignoreCache is set for test requests only)
            if ($ignoreCache === true) {
                $this->addApiCommunicationMessage(array(
                    'request_url'  => $postURL,
                    'request_data' => $xmlData,
                    'response'     => $result,
                ));
            }

            if (!$this->hasError() && !isset($response['err_msg'])) {
                foreach ($response as $code => $_rate) {
                    $rate = new \XLite\Model\Shipping\Rate();

                    $method = $this->getMethodByCode($code, static::STATE_ALL);

                    if ($method && $method->getEnabled()) {
                        // Method is registered and enabled

                        $rate->setMethod($method);
                        $rate->setBaseRate($_rate['amount']);

                        if (!empty($data['cod_enabled'])) {
                            $extraData = new \XLite\Core\CommonCell();
                            $extraData->cod_supported = true;
                            $extraData->cod_rate = $rate->getBaseRate();
                            $rate->setExtraData($extraData);
                        }

                        $rates[] = $rate;
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

    // }}}

    // {{{ Configuration

    /**
     * Returns true if FedEx module is configured
     *
     * @return boolean
     */
    public function isConfigured()
    {
        $config = $this->getConfiguration();

        return $config->meter_number
        && $config->key
        && $config->password
        && $config->account_number;
    }

    /**
     * Get currency conversion rate
     *
     * @return float
     */
    protected function getCurrencyConversionRate()
    {
        $config = $this->getConfiguration();

        return ((float) $config->currency_rate) ?: 1;
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

        // Weight in store weight units
        $limits['weight'] = \XLite\Core\Converter::convertWeightUnits(
            $config->max_weight,
            'lbs',
            \XLite\Core\Config::getInstance()->Units->weight_unit
        );

        list($limits['length'], $limits['width'], $limits['height']) = $config->dimensions;

        return $limits;
    }

    // }}}

    // {{{ Tracking information

    /**
     * This method must return the URL to the detailed tracking information about the package.
     * Tracking number is provided.
     *
     * @param string $trackingNumber Tracking number
     *
     * @return string
     */
    public function getTrackingInformationURL($trackingNumber)
    {
        return 'https://www.fedex.com/apps/fedextrack/index.html';
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
        $list['tracknumbers']   = $trackingNumber;
        $list['ascend_header']  = 1;
        $list['clienttype']     = 'dotcom';
        $list['cntry_code']     = 'us';
        $list['language']       = 'english';

        return $list;
    }

    // }}}

    // {{{ Cache

    /**
     * Get key hash (to use this for caching rates)
     *
     * @param string $key Key value
     *
     * @return string
     */
    protected function getKeyHash($key)
    {
        $key = preg_replace('/<v17:ShipTimestamp>.+<\/v17:ShipTimestamp>/i', '', $key);

        return parent::getKeyHash($key);
    }

    // }}}

    // {{{ Logging

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
                '|<v17:AccountNumber>.+</v17:AccountNumber>|i',
                '|<v17:MeterNumber>.+</v17:MeterNumber>|i',
                '|<v17:Key>.+</v17:Key>|i',
                '|<v17:Password>.+</v17:Password>|i',
            ),
            array(
                '<v17:AccountNumber>xxx</v17:AccountNumber>',
                '<v17:MeterNumber>xxx</v17:MeterNumber>',
                '<v17:Key>xxx</v17:Key>',
                '<v17:Password>xxx</v17:Password>',
            ),
            $data
        );
    }

    // }}}

    // {{{ COD

    /**
     * Check if 'Cash on delivery (FedEx)' payment method enabled
     *
     * @return boolean
     */
    public static function isCODPaymentEnabled()
    {
        $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(array('service_name' => 'COD_FEDEX'));

        return $method && $method->getEnabled();
    }

    /**
     * Check if COD is allowed
     *
     * @param array $data Input data array
     *
     * @return boolean
     */
    protected function isCODAllowed($data)
    {
        return true;
    }

    // }}}

    // {{{ Internals

    /**
     * Check if SmartPost service should be used
     *
     * @param array $fedexOptions FedEx options array
     *
     * @return boolean
     */
    protected function isSmartPost($fedexOptions)
    {
        return isset($fedexOptions['fxsp']) && $fedexOptions['fxsp'];
    }

    /**
     * Check if Ground service should be used
     *
     * @param array $fedexOptions FedEx options array
     *
     * @return boolean
     */
    protected function isGround($fedexOptions)
    {
        return isset($fedexOptions['fdxg']) && $fedexOptions['fdxg'];
    }

    /**
     * Check if Express service should be used
     *
     * @param array $fedexOptions FedEx options array
     *
     * @return boolean
     */
    protected function isExpress($fedexOptions)
    {
        return isset($fedexOptions['fdxe']) && $fedexOptions['fdxe'];
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
        $config = $this->getConfiguration();
        $fedexOptions = $config->getData();

        // Define ship date
        $fedexOptions['ship_date_ready']
            = date('c', \XLite\Core\Converter::time() + ((int) $fedexOptions['ship_date']) * 24 * 3600);

        // Define available carrier codes
        $carrierCodes = '';

        foreach (array('fdxe', 'fdxg', 'fxsp') as $code) {
            if (isset($fedexOptions[$code]) && $fedexOptions[$code]) {
                $carrierCodes
                    .= str_repeat(' ', 9) . '<v17:CarrierCodes>' . strtoupper($code) . '</v17:CarrierCodes>' . PHP_EOL;
            }
        }

        $rateRequestType = isset($fedexOptions['rate_request_type'])
            ? $fedexOptions['rate_request_type']
            : 'NONE';

        // Define address fields
        $fedexOptions['destination_state_code']
            = (isset($data['dstAddress']['state']) ? $data['dstAddress']['state'] : '');

        $fedexOptions['destination_country_code']
            = (isset($data['dstAddress']['country']) ? $data['dstAddress']['country'] : '');

        $fedexOptions['destination_postal_code']
            = (isset($data['dstAddress']['zipcode']) ? $data['dstAddress']['zipcode'] : '');

        $fedexOptions['origin_state_code']
            = (isset($data['srcAddress']['state']) ? $data['srcAddress']['state'] : '');

        $fedexOptions['origin_country_code']
            = (isset($data['srcAddress']['country']) ? $data['srcAddress']['country'] : '');

        $fedexOptions['origin_postal_code']
            = (isset($data['srcAddress']['zipcode']) ? $data['srcAddress']['zipcode'] : '');

        // TODO: Move option to the settings page
        // Shipper address type: 1 - Residential, 0 - Commercial
        $fedexOptions['origin_address_type'] = ($fedexOptions['opt_residential_delivery'] ? 1 : 0);

        // TODO: Add this field to address book and get option from this
        //  address type: 1 - Residential, 0 - Commercial
        $fedexOptions['destination_address_type'] = (
            isset($data['dstAddress']['type'])
            && \XLite\View\FormField\Select\AddressType::TYPE_COMMERCIAL === $data['dstAddress']['type']
        )
            ? 0
            : 1;

        $fedexOptions['dim_units'] = 'IN';
        $fedexOptions['weight_units'] = 'LB';

        $packagesCount = is_array($data['packages']) ? count($data['packages']) : 1;

        // Define packages XML
        $packagesXML = $this->preparePackagesXML($data, $fedexOptions);

        // Define Special services XML
        $specialServicesXML = $this->prepareSpecialServicesShipmentXML($data, $fedexOptions);

        $smartPostDetail = '';
        if ($this->isSmartPost($fedexOptions)) {
            $hubId = $this->isTestMode()
                ? '5531'
                : $fedexOptions['fxsp_hub_id'];
            $indicia = $fedexOptions['fxsp_indicia'];
            $smartPostDetail = $this->prepareSmartPostDetails($indicia, $hubId);
        }

        $recipientContactForGround = '';
        if ($this->isGround($fedexOptions)) {
            $recipientContactForGround = "
                <v17:Contact>
                    <v17:PersonName>Hey</v17:PersonName>
                    <v17:CompanyName>Hey</v17:CompanyName>
                    <v17:PhoneNumber>9822280721</v17:PhoneNumber>
                    <v17:EMailAddress>Hey</v17:EMailAddress>
                </v17:Contact>";
        }

        $result = <<<OUT
<soapenv:Envelope xmlns:soapenv='http://schemas.xmlsoap.org/soap/envelope/' xmlns:v17='http://fedex.com/ws/rate/v17'>
   <soapenv:Header/>
   <soapenv:Body>
      <v17:RateRequest>
         <v17:WebAuthenticationDetail>
            <v17:UserCredential>
               <v17:Key>{$fedexOptions['key']}</v17:Key>
               <v17:Password>{$fedexOptions['password']}</v17:Password>
            </v17:UserCredential>
         </v17:WebAuthenticationDetail>
         <v17:ClientDetail>
            <v17:AccountNumber>{$fedexOptions['account_number']}</v17:AccountNumber>
            <v17:MeterNumber>{$fedexOptions['meter_number']}</v17:MeterNumber>
         </v17:ClientDetail>
         <v17:TransactionDetail>
            <v17:CustomerTransactionId>X-Cart 5: Rate an order packages v17</v17:CustomerTransactionId>
         </v17:TransactionDetail>
         <v17:Version>
            <v17:ServiceId>crs</v17:ServiceId>
            <v17:Major>17</v17:Major>
            <v17:Intermediate>0</v17:Intermediate>
            <v17:Minor>0</v17:Minor>
         </v17:Version>
         <v17:ReturnTransitAndCommit>1</v17:ReturnTransitAndCommit>
{$carrierCodes}
         <v17:RequestedShipment>
            <v17:ShipTimestamp>{$fedexOptions['ship_date_ready']}</v17:ShipTimestamp>
            <v17:DropoffType>{$fedexOptions['dropoff_type']}</v17:DropoffType>
            <v17:PreferredCurrency>{$fedexOptions['currency_code']}</v17:PreferredCurrency>
            <v17:Shipper>
               <v17:AccountNumber>{$fedexOptions['account_number']}</v17:AccountNumber>
               <v17:Address>
                  <v17:StateOrProvinceCode>{$fedexOptions['origin_state_code']}</v17:StateOrProvinceCode>
                  <v17:PostalCode>{$fedexOptions['origin_postal_code']}</v17:PostalCode>
                  <v17:CountryCode>{$fedexOptions['origin_country_code']}</v17:CountryCode>
                  <v17:Residential>{$fedexOptions['origin_address_type']}</v17:Residential>
               </v17:Address>
            </v17:Shipper>
            <v17:Recipient>
               <v17:Address>
                  <v17:StateOrProvinceCode>{$fedexOptions['destination_state_code']}</v17:StateOrProvinceCode>
                  <v17:PostalCode>{$fedexOptions['destination_postal_code']}</v17:PostalCode>
                  <v17:CountryCode>{$fedexOptions['destination_country_code']}</v17:CountryCode>
                  <v17:Residential>{$fedexOptions['destination_address_type']}</v17:Residential>
               </v17:Address>
            </v17:Recipient>
            <v17:ShippingChargesPayment>
               <v17:PaymentType>SENDER</v17:PaymentType>
               <v17:Payor>
                  <v17:ResponsibleParty>
                     <v17:AccountNumber>{$fedexOptions['account_number']}</v17:AccountNumber>
                  </v17:ResponsibleParty>
               </v17:Payor>
            </v17:ShippingChargesPayment>
{$specialServicesXML}
            {$smartPostDetail}
            <v17:RateRequestTypes>{$rateRequestType}</v17:RateRequestTypes>
            <v17:PackageCount>{$packagesCount}</v17:PackageCount>
{$packagesXML}
         </v17:RequestedShipment>
      </v17:RateRequest>
   </soapenv:Body>
</soapenv:Envelope>
OUT;

        return $result;
    }

    /**
     * Smart post details XML gtter
     * @return string
     */
    protected function prepareSmartPostDetails($indicia, $hubId)
    {
        return "
            <v17:SmartPostDetail>
                <v17:Indicia>$indicia</v17:Indicia>
                <v17:HubId>$hubId</v17:HubId>
            </v17:SmartPostDetail>";
    }

    /**
     * Return XML string with packages description
     *
     * @param array $data         Request data
     * @param array $fedexOptions FedEx options array
     *
     * @return string
     */
    protected function preparePackagesXML($data, $fedexOptions)
    {
        $i = 1;
        $itemsXML = '';

        $packages = $data['packages'];

        foreach ($packages as $pack) {
            if ('YOUR_PACKAGING' === $fedexOptions['packaging']) {
                if (isset($pack['box'])) {
                    $length = $pack['box']['length'];
                    $width  = $pack['box']['width'];
                    $height = $pack['box']['height'];

                } else {
                    list($length, $width, $height) = $fedexOptions['dimensions'];
                }

                $dimensionsXML = <<<OUT
               <v17:Dimensions>
                  <v17:Length>{$length}</v17:Length>
                  <v17:Width>{$width}</v17:Width>
                  <v17:Height>{$height}</v17:Height>
                  <v17:Units>{$fedexOptions['dim_units']}</v17:Units>
               </v17:Dimensions>
OUT;
            } else {
                $dimensionsXML = '';
            }

            $weightXML = <<<OUT
               <v17:Weight>
                  <v17:Units>{$fedexOptions['weight_units']}</v17:Units>
                  <v17:Value>{$pack['weight']}</v17:Value>
               </v17:Weight>
OUT;

            // Declared value
            $declaredValueXML = '';

            $subtotal = $this->getPackagesSubtotal($data);

            if (!$this->isSmartPost($fedexOptions)
                && 0 < $subtotal
                && $fedexOptions['send_insured_value']
            ) {
                $declaredValueXML = <<<OUT
               <v17:InsuredValue>
                 <v17:Currency>{$fedexOptions['currency_code']}</v17:Currency>
                 <v17:Amount>{$subtotal}</v17:Amount>
               </v17:InsuredValue>
OUT;
            }

            $specialServicesXML = $this->prepareSpecialServicesPackageXML($data, $fedexOptions);

            $specialServicesXML = str_replace('{{fedex_weight}}', $pack['weight'], $specialServicesXML);

            $itemsXML .= <<<EOT
            <v17:RequestedPackageLineItems>
               <v17:SequenceNumber>{$i}</v17:SequenceNumber>
               <v17:GroupPackageCount>1</v17:GroupPackageCount>
{$declaredValueXML}
{$weightXML}
{$dimensionsXML}
{$specialServicesXML}
            </v17:RequestedPackageLineItems>

EOT;
            $i++;
        } // foreach ($packages as $pack)

        return $itemsXML;
    }

    /**
     * Return XML string with special services description
     *
     * @param array  $data         Input data
     * @param array  $fedexOptions FedEx options array
     *
     * @return string
     */
    protected function prepareSpecialServicesPackageXML($data, $fedexOptions)
    {
        $result = '';
        $specialServices = array();

        if (!empty($fedexOptions['dg_accessibility'])
            && !$this->isSmartPost($fedexOptions)
            && !$this->isGround($fedexOptions)
        ) {
            $specialServices[] = <<<OUT
                 <v17:SpecialServiceTypes>DANGEROUS_GOODS</v17:SpecialServiceTypes>
                 <v17:DangerousGoodsDetail>
                   <v17:Accessibility>{$fedexOptions['dg_accessibility']}</v17:Accessibility>
                 </v17:DangerousGoodsDetail>
OUT;
        }

        /* Option disabled
        if ('Y' == $fedexOptions['dry_ice']) {
            $specialServices[] = <<<OUT
                 <v17:SpecialServiceTypes>DRY_ICE</v17:SpecialServiceTypes>
                 <v17:DryIceWeight>
                   <v17:Units>LB</ns:Units>
                   <v17:Value>{{fedex_weight}}</v17:Value>
                 </v17:DryIceWeight>
OUT;
        }
         */

        /* Option disabled
        if ('Y' == $fedexOptions['opt_nonstandard_container']) {
            $specialServices[] = <<<OUT
                 <v17:SpecialServiceTypes>NON_STANDARD_CONTAINER</v17:SpecialServiceTypes>
OUT;
        }
         */

        if (!empty($fedexOptions['signature'])) {
            $specialServices[] = <<<OUT
                 <v17:SignatureOptionDetail>
                   <v17:OptionType>{$fedexOptions['signature']}</v17:OptionType>
                 </v17:SignatureOptionDetail>
OUT;
        }

        if (!empty($specialServices)) {
            $specialServicesString = implode('', $specialServices);
            $result =<<<OUT
               <v17:SpecialServicesRequested>
{$specialServicesString}
               </v17:SpecialServicesRequested>
OUT;
        }

        return $result;
    }

    /**
     * Return XML string with special services description
     *
     * @param array $data         Input data
     * @param array $fedexOptions FedEx options array
     *
     * @return string
     */
    protected function prepareSpecialServicesShipmentXML($data, $fedexOptions)
    {
        $result = '';
        $specialServices = array();
        $specialServicesTypes = array();

        if (!empty($data['cod_enabled']) && $this->isCODAllowed($data)) {
            $subtotal = $this->getPackagesSubtotal($data);

            if (empty($fedexOptions['cod_type'])) {
                $fedexOptions['cod_type'] = 'ANY';
            }

            $specialServices[] = <<<OUT
                <v17:SpecialServiceTypes>COD</v17:SpecialServiceTypes>
                <v17:CodDetail>
                  <v17:CodCollectionAmount>
                    <v17:Currency>{$fedexOptions['currency_code']}</v17:Currency>
                    <v17:Amount>{$subtotal}</v17:Amount>
                  </v17:CodCollectionAmount>
                  <v17:CollectionType>{$fedexOptions['cod_type']}</v17:CollectionType>
                </v17:CodDetail>
OUT;
        }

        if ($fedexOptions['opt_saturday_pickup']
            && 6 == date('w', \XLite\Core\Converter::time() + ((int) $fedexOptions['ship_date']) * 24 * 3600)
        ) {
            $specialServicesTypes[] = 'SATURDAY_PICKUP';
        }

        foreach ($specialServicesTypes as $type) {
            $specialServices[] = <<<OUT
                <v17:SpecialServiceTypes>{$type}</v17:SpecialServiceTypes>
OUT;
        }

        if (!empty($specialServices)) {
            $specialServicesString = implode('', $specialServices);
            $result =<<<OUT
            <v17:SpecialServicesRequested>
{$specialServicesString}
            </v17:SpecialServicesRequested>
OUT;
        }

        return $result;
    }

    /**
     * Parses response and returns an associative array
     *
     * @param string $stringData Response received from FedEx
     *
     * @return array
     */
    protected function parseResponse($stringData)
    {
        $result = array();

        $xml = \XLite\Core\XML::getInstance();

        $xmlParsed = $xml->parse($stringData, $err);

        if (isset($xmlParsed['soapenv:Envelope']['#']['soapenv:Body'][0]['#']['soapenv:Fault'][0]['#'])) {
            // FedEx responses with error of request validation

            $result['err_msg']= $xml->getArrayByPath(
                $xmlParsed,
                'soapenv:Envelope/#/soapenv:Body/0/#/soapenv:Fault/0/#/faultstring/0/#'
            );

        } else {
            $rateReply = $xml->getArrayByPath($xmlParsed, 'SOAP-ENV:Envelope/#/SOAP-ENV:Body/0/#/RateReply/0/#');

            $errorCodes = array('FAILURE','ERROR');

            if (in_array($xml->getArrayByPath($rateReply, 'HighestSeverity/0/#'), $errorCodes, true)) {
                // FedEx failed to return valid rates

                $result['err_msg'] = $xml->getArrayByPath($rateReply, 'Notifications/0/#/Message/0/#');
                $result['err_code'] = $xml->getArrayByPath($rateReply, 'Notifications/0/#/Code/0/#');

            } else {
                // Success

                $rateDetails = $xml->getArrayByPath($rateReply, 'RateReplyDetails');

                if (!empty($rateDetails) && is_array($rateDetails)) {
                    $conversionRate = $this->getCurrencyConversionRate();

                    $resultRates = array();

                    foreach ($rateDetails as $rate) {
                        $serviceType = $xml->getArrayByPath($rate, '#/ServiceType/0/#');

                        $ratedShipmentDetails = $xml->getArrayByPath($rate, '#/RatedShipmentDetails');

                        foreach ($ratedShipmentDetails as $rateDetails) {

                            $rateType = $xml->getArrayByPath(
                                $rateDetails,
                                '#/ShipmentRateDetail/RateType/0/#'
                            );

                            $resultRates[$serviceType][$rateType]['amount'] = $this->getRateAmount($rateDetails);

                            $variableHandlingCharge = $xml->getArrayByPath(
                                $rate,
                                '#/ShipmentRateDetail/TotalVariableHandlingCharges/VariableHandlingCharge/Amount/0/#'
                            );

                            $resultRates[$serviceType][$rateType]['amount'] += (float) $variableHandlingCharge;

                            if (1 != $conversionRate) {
                                $resultRates[$serviceType][$rateType]['amount'] *= $conversionRate;
                            }
                        }
                    }

                    $config = $this->getConfiguration();
                    $fedexOptions = $config->getData();

                    $prefferedType = 'PAYOR_' . ('LIST' == $fedexOptions['rate_request_type'] ? 'LIST' : 'ACCOUNT') . '_PACKAGE';

                    foreach ($resultRates as $service => $serviceData) {

                        if (isset($serviceData[$prefferedType])) {
                            // Preffered request type is found - save this
                            $result[$service]['amount'] = $serviceData[$prefferedType]['amount'];

                        } else {
                            // Preffered request type is found - search first available amount
                            foreach ($serviceData as $rateType => $rateData) {
                                $result[$service]['amount'] = $rateData['amount'];
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Log error
        if (isset($result['err_msg'])) {
            $this->log(array(
                'Error'    => $result['err_msg'],
                'Response' => \XLite\Core\XML::getInstance()->getFormattedXML($stringData)
            ));
        }

        return $result;
    }

    /**
     * Get shipping rate
     *
     * @param array $entry
     *
     * @return array
     */
    protected function getRateAmount($entry)
    {
        $xml = \XLite\Core\XML::getInstance();

        $config = $this->getConfiguration();
        $currencyCode = $config->currency_code;

        $rateCurrency
            = $xml->getArrayByPath($entry, 'ShipmentRateDetail/TotalNetCharge/Currency/0/#');

        if ($rateCurrency != $currencyCode) {
            // Currency conversion is needed
            $ratedShipmentDetails = $entry;

            // Try to find extact rate value
            $preciseRateFound = false;

            foreach ($ratedShipmentDetails as $key => $shipmentRateDetail) {
                $currencyExchangeRate =
                    $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/CurrencyExchangeRate/RATE/0/#');
                $fromCurrency = $xml->getArrayByPath(
                    $shipmentRateDetail,
                    'ShipmentRateDetail/CurrencyExchangeRate/FromCurrency/0/#'
                );
                $rateCurrency =
                    $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/TotalNetCharge/Currency/0/#');
                $estimatedRate =
                    $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/TotalNetCharge/Amount/0/#');

                if ($currencyExchangeRate == '1.0'
                    && $fromCurrency == $currencyCode
                    && $rateCurrency == $currencyCode
                ) {
                    // This rate type can be used without conversion
                    $preciseRateFound = true;
                    break;
                }
            }

            if (!$preciseRateFound) {
                // Rate type without conversion is not found/ Use conversion
                foreach ($ratedShipmentDetails as $key => $shipmentRateDetail) {
                    $currencyExchangeRate =
                        $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/CurrencyExchangeRate/RATE/0/#');

                    if (0 == $currencyExchangeRate) {
                        continue;
                    }

                    $fromCurrency = $xml->getArrayByPath(
                        $shipmentRateDetail,
                        'ShipmentRateDetail/CurrencyExchangeRate/FromCurrency/0/#'
                    );

                    $intoCurrency = $xml->getArrayByPath(
                        $shipmentRateDetail,
                        'ShipmentRateDetail/CurrencyExchangeRate/IntoCurrency/0/#'
                    );

                    $rateCurrency =
                        $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/TotalNetCharge/Currency/0/#');

                    $estimatedRate =
                        $xml->getArrayByPath($shipmentRateDetail, 'ShipmentRateDetail/TotalNetCharge/Amount/0/#');

                    if ($fromCurrency == $rateCurrency) {
                        $estimatedRate *= $currencyExchangeRate;
                        break;

                    } elseif ($intoCurrency == $rateCurrency) {
                        $estimatedRate /= $currencyExchangeRate;
                        break;
                    }
                }
            }

        } // if ($rateCurrency != $currencyCode) {

        if (empty($estimatedRate)) {
            $estimatedRate
                = $xml->getArrayByPath($entry, 'ShipmentRateDetail/TotalNetCharge/Amount/0/#');
        }

        return $estimatedRate;
    }

    /**
     * Get sum of subtotals of all packages
     *
     * @param array $data Input data
     *
     * @return float
     */
    protected function getPackagesSubtotal($data)
    {
        $subtotal = 0;

        if (is_array($data)) {
            foreach ($data['packages'] as $pack) {
                $subtotal += (float) $pack['price'];
            }
        }

        return round($subtotal / $this->getCurrencyConversionRate(), 2);
    }

    // }}}
}
