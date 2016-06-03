<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Model\Shipping\Processor;

/**
 * Shipping processor model
 *
 * @see http://www.auctioninc.com/info/page/getting_started_api
 */
class AuctionInc extends \XLite\Model\Shipping\Processor\AProcessor
{
    /**
     * Cache ttl (for wrong response)
     */
    const CACHE_TTL = 1800;

    /**
     * Trial account id
     */
    const ACCOUNT_ID = '2fac8a0a8969284c8d27c29cb6e5d0fe';

    /**
     * API connector
     *
     * @var \ShipRateAPI
     */
    protected $APIConnector;

    /**
     * Enabled shipping methods
     *
     * @var array
     */
    protected $enabledMethods;

    /**
     * Enabled shipping carriers
     *
     * @var array
     */
    protected $enabledCarriers;

    /**
     * Response cache
     *
     * @var array
     */
    protected $response = array();

    /**
     * Returns processor Id
     *
     * @return string
     */
    public function getProcessorId()
    {
        return 'auctionInc';
    }

    /**
     * Returns processor name
     *
     * @return string
     */
    public function getProcessorName()
    {
        return 'ShippingCalc';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSignUpURL()
    {
        return !$this->isSSAvailable()
            ? 'https://www.auctioninc.com/info/page/xcart_shippingcalc'
            : '';
    }

    /**
     * Returns url for sign up
     *
     * @return string
     */
    public function getSettingsURL()
    {
        return \XLite\Module\XC\AuctionInc\Main::getSettingsForm();
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
        $config = $this->getConfiguration();
        /** @var \XLite\Model\Repo\State $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Model\State');

        $sourceAddress = $inputData->getOrder()->getSourceAddress();

        $result['srcAddress'] = array(
            'country' => $sourceAddress->getCountryCode(),
            'zipcode' => $sourceAddress->getZipcode(),
            'state'   => $sourceAddress->getState()->getCode(),
        );

        $destinationAddress = \XLite\Model\Shipping::getInstance()->getDestinationAddress($inputData);
        if (null !== $destinationAddress) {
            $stateCode = $repo->getCodeById($destinationAddress['state']);

            $shippingAddress = $inputData->getOrder()->getProfile()
                ? $inputData->getOrder()->getProfile()->getShippingAddress()
                : null;

            $type = $shippingAddress && $shippingAddress->getType()
                ? $shippingAddress->getType()
                : $config->destinationType;

            $result['dstAddress'] = array(
                'country' => $destinationAddress['country'],
                'zipcode' => $destinationAddress['zipcode'],
                'state'   => $stateCode,
                'type'    => $type,
            );

            $result['items'] = $this->getItems($inputData);
        }

        return array('package' => $result);
    }

    /**
     * Prepare input data from array
     *
     * @param array $inputData Array of input data (from test controller)
     *
     * @return array
     */
    protected function prepareDataFromArray(array $inputData)
    {
        $data = $inputData;
        $config = $this->getConfiguration();
        $package = $data['package'];

        $item = array(
            'calculationMethod' => 'C',
            'sku'              => 'TEST',
            'name'              => 'Test item',
            'qty'               => 1,
            'weight'            => $package['weight'],
            'dimensions'        => array($package['length'], $package['width'], $package['height']),
            'weightUOM'         => 'LB',
            'dimensionsUOM'     => 'IN',
            'price'             => $package['subtotal'],
            'package'           => $config->package,
        );

        $package['items'] = array($item);

        $data['package'] = $package;

        return $data;
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

        $key = $this->getConfigurationFingerPrint() . serialize($data);

        if (!$ignoreCache) {
            $cachedResponse = $this->getDataFromCache($key);
        }

        $response = null;
        if (null !== $cachedResponse) {
            $response = $cachedResponse;

        } elseif (!\XLite\Model\Shipping::isIgnoreLongCalculations()) {
            $APIConnector = $this->getAPIConnector();
            $this->setRequestData($data);

            $status = false;

            if ($this->isSSAvailable()) {
                $status = $APIConnector->GetItemShipRateSS($response);

            } elseif ($this->isXSAvailable()) {
                $methods = $this->getMethods(static::STATE_ENABLED_ONLY);
                if (count($methods)) {
                    $status = $APIConnector->GetItemShipRateXS($response);
                }
            }

            $this->logResponse($status, $data, $response);

            if ($status) {
                $this->saveDataInCache($key, $response);

                // drop selected shipping method to set it to cheapest
                if (!\XLite::isAdminZone()) {
                    /** @var \XLite\Model\Cart $cart */
                    $cart = \XLite::getController()->getCart();
                    $cart->setShippingId(0);
                    if ($cart->getProfile()) {
                        $cart->setLastShippingId(0);
                    }
                }
            } elseif (isset($response['ErrorList'])) {
                // report error
                $errorMessages = array();
                foreach ($response['ErrorList'] as $error) {
                    $errorMessages[] = $error['Message'];
                }

                $this->setError(implode('; ', $errorMessages));
            }
        }

        if ($response && !$this->hasError()) {
            $rates = $this->parseResponse($response);

        } else {
            $this->saveDataInCache($key, $response, static::CACHE_TTL);
        }

        if (!$response || empty($rates)) {
            // Apply fallback rate
            if (empty($rates) && 'N' !== $config->fallbackRate) {
                $rateValue = $this->getFallbackRateValue($data['package']);

                $rate = new \XLite\Model\Shipping\Rate();
                $rate->setBaseRate($rateValue);

                $method = $this->createMethod('FF_FIXEDFEE', 'Fixed fee', true);

                $rate->setMethod($method);
                $rates[] = $rate;

                $this->setError();
            }
        }

        return $rates;
    }

    // }}}

    /**
     * Collect items data from modifier
     *
     * @param \XLite\Logic\Order\Modifier\Shipping $modifier Shipping order modifier
     *
     * @return array
     */
    protected function getItems($modifier)
    {
        $result = array();

        /** @var \XLite\Model\OrderItem $item */
        foreach ($modifier->getItems() as $item) {
            $product = $item->getProduct();
            /** @var \XLite\Module\XC\AuctionInc\Model\ProductAuctionInc $auctionIncData */
            $auctionIncData = $product->getAuctionIncData()
                ?: new \XLite\Module\XC\AuctionInc\Model\ProductAuctionInc();

            $onDemand = array_map(function ($a) {
                list(, $serviceCode) = explode('_', $a);

                return $serviceCode;
            }, $auctionIncData->getOnDemand());

            $resultItem = array(
                'calculationMethod'      => $auctionIncData->getCalculationMethod(),
                'sku'                    => $product->getSku(),
                'name'                   => $product->getName(),
                'qty'                    => (int) $item->getAmount(),
                'weight'                 => $product->getWeight(),
                'weightUOM'              => $auctionIncData->getWeightUOM(),
                'dimensions'             => $auctionIncData->getDimensions(),
                'dimensionsUOM'          => $auctionIncData->getDimensionsUOM(),
                'package'                => $auctionIncData->getPackage(),
                'originCode'             => $auctionIncData->getOriginCode(),
                'onDemand'               => implode(', ', $onDemand),
                'carrierAccessorialFees' => implode(', ', $auctionIncData->getCarrierAccessorialFees())
            );

            $resultItem['price'] = ('Y' === $auctionIncData->getInsurable())
                ? ($item->getTotal() / $item->getAmount())
                : 0;

            if ('F' === $auctionIncData->getSupplementalItemHandlingMode()) {
                $resultItem['supplementalItemHandlingFee'] = $auctionIncData->getSupplementalItemHandlingFee();

            } elseif ('C' === $auctionIncData->getSupplementalItemHandlingMode()) {
                $resultItem['supplementalItemHandlingCode'] = $auctionIncData->getSupplementalItemHandlingCode();
            }

            if ('F' === $auctionIncData->getFixedFeeMode()) {
                $resultItem['fixedFee1'] = $auctionIncData->getFixedFee1();
                $resultItem['fixedFee2'] = $auctionIncData->getFixedFee2();

            } elseif ('C' === $auctionIncData->getFixedFeeMode()) {
                $resultItem['fixedFeeCode'] = $auctionIncData->getFixedFeeCode();
            }

            $result[] = $resultItem;
        }

        return $result;
    }

    /**
     * Set request data to API connector
     *
     * @param array $data Request data
     *
     * @return void
     */
    protected function setRequestData($data)
    {
        $APIConnector = $this->getAPIConnector();
        $config = $this->getConfiguration();

        $package = $data['package'];

        // SSL currently not supported
        $APIConnector->setSecureComm(false);

        // curl option (use only if you have the libcurl package installed)
        $APIConnector->useCURL(false);

        //************************************************************
        // Set the Detail Level (1, 2 or 3) (Default = 1)
        // DL 1:  minimum required data returned
        // DL 2:  shipping rate components included
        // DL 3:  package-level detail included
        //************************************************************
        $detailLevel = 3;
        $APIConnector->setDetailLevel($detailLevel);

        //************************************************************
        // Set Currency
        // Determines the currency of the returned rates
        // and the expected currency of any monetary values set in your call
        // (declared value, item handling fee, fixed fees)
        //************************************************************
        $APIConnector->setCurrency(\XLite::getInstance()->getCurrency()->getCode());

        //************************************************************
        // Set Header Reference Code (optional)
        // can be used to identify and track a subset of calls,
        // such as a particular seller
        // (trackable in AuctionInc acct => API Statistics)
        //************************************************************
        if ($this->isXSAvailable()) {
            $APIConnector->setHeaderRefCode($this->getHeaderReferenceCode());
        }

        //**************************************************************
        // Set Origin Address/es for this Seller
        // (typically fed in from your seller account configuration)
        //**************************************************************
        if ($package['srcAddress'] && $this->isXSAvailable()) {
            $APIConnector->addOriginAddress(
                $package['srcAddress']['country'],
                $package['srcAddress']['zipcode'],
                $package['srcAddress']['state']
            );
        }

        //************************************************************
        // Set Destination Address for this API call
        // (These values would typically come from your cart)
        //************************************************************
        if ($package['dstAddress']) {
            $APIConnector->setDestinationAddress(
                $package['dstAddress']['country'],
                $package['dstAddress']['zipcode'],
                $package['dstAddress']['state'],
                \XLite\View\FormField\Select\AddressType::TYPE_RESIDENTIAL === $package['dstAddress']['type']
            );
        }

        $this->setItemsData($package['items']);

        //*************************************************************
        // Set Carriers/Services to rate for this shipment
        // (on-demand flag is optional, see documentation)
        // (typically fed in from your seller account configuration)
        //*************************************************************
        $enabledCarriers = $this->getEnabledCarriers();
        foreach ($enabledCarriers as $carrier) {
            $entryPoint = $config->{'entryPoint' . $carrier};
            $APIConnector->addCarrier($carrier, $entryPoint);
        }

        $methods = $this->getMethods(static::STATE_ENABLED_ONLY);
        /** @var \XLite\Model\Shipping\Method $method */
        foreach ($methods as $method) {
            list($carrierCode, $serviceCode) = explode('_', $method->getCode());

            $APIConnector->addService($carrierCode, $serviceCode, $method->getOnDemand());
        }
    }

    /**
     * Set (add) items data to API connector
     *
     * @param array $items Items
     *
     * @return void
     */
    protected function setItemsData($items)
    {
        foreach ($items as $item) {
            if ('C' === $item['calculationMethod'] || 'CI' === $item['calculationMethod']) {
                $this->setItemCarrierCalculation($item);

            } elseif ('F' === $item['calculationMethod']) {
                $this->setItemFixedFee($item);

            } elseif ('N' === $item['calculationMethod']) {
                $this->setItemFree($item);
            }
        }
    }

    /**
     * Set (add) item data to API connector
     *
     * @param array $item Item
     *
     * @return void
     */
    protected function setItemCarrierCalculation($item)
    {
        $APIConnector = $this->getAPIConnector();

        $dimensions = $item['dimensions'];

        $APIConnector->addItemCalc(
            $item['sku'],
            $item['qty'],
            $item['weight'],
            $item['weightUOM'],
            $dimensions[0],
            $dimensions[1],
            $dimensions[2],
            $item['dimensionsUOM'],
            $item['price'],
            $item['package'],
            1,
            $item['calculationMethod']
        );

        if ($this->isSSAvailable()
            && isset($item['originCode'])
            && 'default' !== $item['originCode']
        ) {
            $APIConnector->addItemOriginCode($item['originCode']);
        }

        if (isset($item['onDemand']) && $item['onDemand']) {
            $APIConnector->addItemOnDemandServices($item['onDemand']);
        }

        if (isset($item['carrierAccessorialFees']) && $item['carrierAccessorialFees']) {
            $APIConnector->addItemSpecialCarrierServices($item['carrierAccessorialFees']);
        }

        if (isset($item['supplementalItemHandlingFee'])) {
            $APIConnector->addItemHandlingFee($item['supplementalItemHandlingFee']);

        } elseif (isset($item['supplementalItemHandlingCode'])) {
            $APIConnector->addItemSuppHandlingCode($item['supplementalItemHandlingCode']);
        }
    }

    /**
     * Set (add) item data to API connector
     *
     * @param array $item Item
     *
     * @return void
     */
    protected function setItemFixedFee($item)
    {
        $APIConnector = $this->getAPIConnector();

        if (isset($item['fixedFee1'], $item['fixedFee2'])) {
            $fixedFeeMode = 'F';
            $fixedFee1    = $item['fixedFee1'];
            $fixedFee2    = $item['fixedFee2'];
            $fixedFeeCode = '';
        } else {
            $fixedFeeMode = 'C';
            $fixedFee1    = 0;
            $fixedFee2    = 0;
            $fixedFeeCode = $item['fixedFeeCode'];
        }

        $APIConnector->addItemFixed(
            $item['name'],
            $item['qty'],
            $fixedFeeMode,
            $fixedFee1,
            $fixedFee2,
            $fixedFeeCode
        );

        if (isset($item['originCode'])) {
            $APIConnector->addItemOriginCode($item['originCode']);
        }
    }

    /**
     * Set (add) item data to API connector
     *
     * @param array $item Item
     *
     * @return void
     */
    protected function setItemFree($item)
    {
        $APIConnector = $this->getAPIConnector();
        $APIConnector->addItemFree($item['name'], $item['qty']);
    }

    /**
     * Returns rates array parsed from response
     *
     * @param array $response
     *
     * @return array
     */
    protected function parseResponse($response)
    {
        $rates = array();

        if (isset($response['ShipRate'])) {
            foreach ($response['ShipRate'] as $responseRate) {
                // UPS Next Day Air Early AM is a commercial only service.
                // Rather than ask you to implement differential code based
                // on the module Residential setting, lets just eliminate
                // this service method for the XS trial.
                // The two “Saturday” services have special handling in AuctionInc.
                // It would be best just to eliminate these two service methods as well for the XS trial
                $code = $responseRate['CarrierCode'] . '_' . $responseRate['ServiceCode'];
                if (!$this->isSSAvailable()
                    && in_array($code, array('UPS_UPSNDE', 'FEDEX_FDXPOS', 'UPS_UPSNDAS'), true)
                ) {
                    continue;
                }

                $rate = new \XLite\Model\Shipping\Rate();
                $rate->setBaseRate($responseRate['Rate']);
                $extraData = new \XLite\Core\CommonCell(array('auctionIncPackage' => $responseRate['PackageDetail']));
                $rate->setExtraData($extraData);

                $method = $this->createMethod(
                    $responseRate['CarrierCode'] . '_' . $responseRate['ServiceCode'],
                    $responseRate['ServiceName'],
                    true
                );

                if ($method
                    && ($this->isSSAvailable() || $method->getEnabled())
                ) {
                    $rate->setMethod($method);
                    $rates[] = $rate;
                }
            }
        }

        return $rates;
    }

    /**
     * Calculate fallback rate value
     *
     * @param array $package Package
     *
     * @return float
     */
    protected function getFallbackRateValue($package)
    {
        $result = 0;
        $config = $this->getConfiguration();
        $fallbackRateValue = $config->fallbackRateValue;

        if ('O' === $config->fallbackRate) {
            $result = $fallbackRateValue;

        } elseif ('I' === $config->fallbackRate) {
            foreach ($package['items'] as $item) {
                $result += $fallbackRateValue * $item['qty'];
            }
        }

        return $result;
    }

    /**
     * Check if SS available
     *
     * @return boolean
     */
    protected function isSSAvailable()
    {
        return (bool) $this->getConfiguration()->accountId;
    }

    /**
     * Check if XS available
     *
     * @return boolean
     */
    protected function isXSAvailable()
    {
        return !$this->isSSAvailable()
            && $this->isXSTrialPeriodValid();
    }

    /**
     * Check XS trial period
     *
     * @return boolean
     */
    protected function isXSTrialPeriodValid()
    {
        $firstUsageDate = $this->getConfiguration()->firstUsageDate;
        $result = true;

        if ($firstUsageDate) {
            $result = LC_START_TIME < $firstUsageDate + \XLite\Module\XC\AuctionInc\Main::TRIAL_PERIOD_DURATION;

        } else {
            $this->generateFirstUsageDate();
        }

        return $result;
    }

    /**
     * Generate first usage date
     *
     * @return void
     */
    protected function generateFirstUsageDate()
    {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(array(
            'category' => 'XC\AuctionInc',
            'name' => 'firstUsageDate',
            'value' => LC_START_TIME,
        ));
    }

    /**
     * Generate first usage date
     *
     * @return string
     */
    protected function generateHeaderReferenceCode()
    {
        $code = 'XC5-' . md5(LC_START_TIME);
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(array(
            'category' => 'XC\AuctionInc',
            'name' => 'headerReferenceCode',
            'value' => $code,
        ));

        return $code;
    }

    /**
     * Returns API connector
     *
     * @return \ShipRateAPI
     */
    protected function getAPIConnector()
    {
        if (null === $this->APIConnector) {
            require_once LC_DIR_MODULES . 'XC' . LC_DS . 'AuctionInc' . LC_DS . 'lib' . LC_DS . 'ShipRateAPI.inc';

            $this->APIConnector = new \ShipRateAPI($this->getAccountId());
        }

        return $this->APIConnector;
    }

    /**
     * Returns account id
     *
     * @return string
     */
    protected function getAccountId()
    {
        $config = $this->getConfiguration();

        return $config->accountId ?: static::ACCOUNT_ID;
    }

    /**
     * Returns header reference code
     *
     * @return string
     */
    protected function getHeaderReferenceCode()
    {
        return $this->getConfiguration()->headerReferenceCode
            ?: $this->generateHeaderReferenceCode();
    }

    /**
     * Add log message
     *
     * @param boolean $status   Status
     * @param array   $request  Request data
     * @param array   $response Response data
     *
     * @return void
     */
    protected function logResponse($status, $request, $response)
    {
        $config = $this->getConfiguration();

        if ($config->debugMode) {
            \XLite\Logger::logCustom('AuctionInc', array(
                'status' => $status,
                'request' => $request,
                'response' => $response
            ));
        }
    }

    /**
     * Returns configuration fingerprint
     *
     * @return string
     */
    protected function getConfigurationFingerPrint()
    {
        return serialize($this->getConfiguration()->getData());
    }

    /**
     * Return enabled carriers
     *
     * @return array
     */
    protected function getEnabledCarriers()
    {
        if (null === $this->enabledCarriers) {
            $config = $this->getConfiguration();

            $carriers = array('DHL', 'FEDEX', 'UPS', 'USPS');
            $this->enabledCarriers = array();
            foreach ($carriers as $carrier) {
                $entryPoint = $config->{'entryPoint' . $carrier};
                if (\XLite\Module\XC\AuctionInc\View\FormField\Select\AEntryPoint::STATE_DISABLED !== $entryPoint) {
                    $this->enabledCarriers[] = $carrier;
                }
            }
        }

        return $this->enabledCarriers;
    }
}
