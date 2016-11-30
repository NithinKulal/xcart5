<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Core;

/**
 * Canada Post API requests
 */
class API extends \XLite\Base\Singleton
{
    /**
     * Quote types
     */
    const QUOTE_TYPE_CONTRACTED     = 'C';
    const QUOTE_TYPE_NON_CONTRACTED = 'N';

    /**
     * Accept-language header possible values
     */
    const ACCEPT_LANGUAGE_EN = 'en-CA';
    const ACCEPT_LANGUAGE_FR = 'fr-CA';

    /**
     * Pick up types
     */
    const PICKUP_TYPE_AUTO   = 'A';
    const PICKUP_TYPE_MANUAL = 'M';

    /**
     * Current configuration
     *
     * @var \XLite\Core\ConfigCell
     */
    protected static $configuration;

    /**
     * Request timeout
     *
     * @var integer
     */
    protected $requestTimeout = 100;

    /**
     * X-Cart Platform ID (PRODUCTION)
     *
     * @var string
     */
    protected $platformId = '8186496';

    /**
     * X-Cart Platform API Key (PRODUCTION)
     *
     * @var string
     */
    protected $platformAPIKey = '4655058ec9109a40:c420e001dfcf0f62c46b27';

    /**
     * X-Cart Platform API Key (DEVELOPMENT)
     *
     * @var string
     */
    protected $platformAPIKeyDev = '7c0c6d7c7f25c3c2:535bb91c3de76bc7b0140d';

    /**
     * Merchant registration URL (PRODUCTION)
     *
     * @var string
     */
    protected $merchantRegUrl = 'https://www.canadapost.ca/cpotools/apps/drc/merchant';

    /**
     * Merchant registration URL (DEVELOPMENT)
     *
     * @var string
     */
    protected $merchantRegUrlDev = 'https://www.canadapost.ca/cpotools/apps/drc/testMerchant';

    /**
     * Canada Post development host
     *
     * @var string
     */
    protected $developmentHost = 'ct.soa-gw.canadapost.ca';

    /**
     * Canada Post production host
     *
     * @var string
     */
    protected $productionHost = 'soa-gw.canadapost.ca';

    /**
     * Canada Post "Get Rates" URL template
     *
     * @var string
     */
    protected $ratesEndpoint = 'https://XX/rs/ship/price';

    /**
     * Canada Post "Create Non-Contract Shipment" URL template
     *
     * @var string
     */
    protected $ncshipmentEndpoint = 'https://XX/rs/{mailed by customer}/ncshipment';

    /**
     * Canada Post "Create Contract Shipment" URL template
     *
     * @var string
     */
    protected $shipmentEndpoint = 'https://XX/rs/{mailed by customer}/{mobo}/shipment';

    /**
     * Canada Post "Transmit Shipments" URL template
     *
     * @var string
     */
    protected $transmitShipmentsEndpoint = 'https://XX/rs/{mailed by customer}/{mobo}/manifest';

    /**
     * Get Canada Post X-Cart Platform ID
     *
     * @return string
     */
    public function getPlatformId()
    {
        return $this->platformId;
    }

    /**
     * Get Canada Post API key
     *
     * @param boolean $isDevelopment Flag - is development mode or not (OPTIONAL)
     *
     * @return \XLite\Core\CommonCell
     */
    public function getCapostAPIkey($isDevelopment = false)
    {
        $capostAPIKey = ($isDevelopment) ? $this->platformAPIKeyDev : $this->platformAPIKey;

        $tmp = explode(':', $capostAPIKey);

        return new \XLite\Core\CommonCell(
            array(
                'user'     => $tmp[0],
                'password' => $tmp[1],
            )
        );
    }

    /**
     * Get accept language
     *
     * @return string
     */
    public function getAcceptLanguage()
    {
        return static::ACCEPT_LANGUAGE_EN;
    }

    // {{{ Canada Post API endpoints and hosts

    /**
     * Get Canada Post merchant registration URL
     *
     * @param boolean $isDevelopment Flag - is development mode or not
     *
     * @return string
     */
    public function getMerchantRegUrl($isDevelopment = null)
    {
        if (!isset($isDevelopment)) {
            $isDevelopment = static::getCanadaPostConfig()->developer_mode;
        }

        return ($isDevelopment) ? $this->merchantRegUrlDev : $this->merchantRegUrl;
    }

    /**
     * Get Canada Post API host
     *
     * @param boolean $isDevelopment Flag - is development mode or not
     *
     * @return string
     */
    public function getApiHost($isDevelopment = false)
    {
        return ($isDevelopment) ? $this->developmentHost : $this->productionHost;
    }

    /**
     * Get "Get Rates" request endpoint
     *
     * @return string
     */
    public function getGetRatesEndpoint()
    {
        return $this->prepareEndpoint($this->ratesEndpoint);
    }

    /**
     * Get "Create Non-Contract Shipment" request endpoint
     *
     * @return string
     */
    public function getCreateNCShipmentEndpoint()
    {
        return $this->prepareEndpoint($this->ncshipmentEndpoint);
    }

    /**
     * Get "Create Shipment" request endpoint
     *
     * @return string
     */
    public function getCreateShipmentEndpoint()
    {
        return $this->prepareEndpoint($this->shipmentEndpoint);
    }

    /**
     * Get "Transmit Shipments" request endpoint
     *
     * @return string
     */
    public function getTransmitShipmentsEndpoint()
    {
        return $this->prepareEndpoint($this->transmitShipmentsEndpoint);
    }

    /**
     * Prepare endpoint (add common data)
     *
     * @param string $endpoint URL template
     *
     * @return string
     */
    protected function prepareEndpoint($endpoint)
    {
        $mailedByCustomer = static::getCanadaPostConfig()->customer_number;

        if (static::isOnBehalfOfAMerchant()) {
            $mailedByCustomer .= '-' . $this->getPlatformId();
        }

        return str_replace(
            array(
                'XX',
                '{mailed by customer}',
                '{mobo}'
            ),
            array(
                $this->getApiHost(static::getCanadaPostConfig()->developer_mode),
                $mailedByCustomer,
                static::getCanadaPostConfig()->customer_number
            ),
            $endpoint
        );
    }

    // }}}

    // {{{ Canada Post API requests

    /**
     * Call Create Non-Contract Shipment request
     *
     * Reason to Call:
     * To allow Canada Post customers without a commercial contract to request and pay for a shipping label.
     *
     * More info at:
     * https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/onestepshipping/createshipment.jsf
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return \XLite\Core\CommonCell
     */
    public function callCreateNCShipmentRequest(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        // Get request XML data
        $requestData = $this->getNCShipmentXmlData($parcel);

        $apiHost = $this->getCreateNCShipmentEndpoint();

        $result = new \XLite\Core\CommonCell();

        try {
            $request = new \XLite\Core\HTTP\Request($apiHost);
            $request->requestTimeout = $this->requestTimeout;
            $request->body = $requestData;
            $request->verb = 'POST';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/vnd.cpc.ncshipment+xml');
            $request->setHeader('Content-Type', 'application/vnd.cpc.ncshipment+xml');
            $request->setHeader('Accept-language', static::ACCEPT_LANGUAGE_EN);

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (isset($response->body)
                && !empty($response->body)
            ) {
                // Parse response to object
                $result = $this->parseResponse($response->body);

            } else {
                $result->errors = array(
                    'INTERNAL' => sprintf('Error while connecting to the Canada Post host (%s) during "Create Non-Contract Shipment" request', $apiHost),
                );
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($apiHost, 'Create Non-Contract Shipment', $requestData, $response->body);
            }

        } catch (\Exception $e) {
            if (!isset($result->errors)) {
                $result->errors = array();
            }

            $result->errors += array($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    /**
     * Call Create Shipment request
     *
     * Reason to Call:
     * To initiate generation of a shipping label by providing shipment details.
     * Use of this service indicates an intention to pay for shipment of an item.
     *
     * More info at:
     * https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/shippingmanifest/createshipment.jsf
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel
     *
     * @return \XLite\Core\CommonCell
     */
    public function callCreateShipmentRequest(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        // Get request XML data
        $requestData = $this->getShipmentXmlData($parcel);

        $apiHost = $this->getCreateShipmentEndpoint();

        $result = new \XLite\Core\CommonCell();

        try {
            $request = new \XLite\Core\HTTP\Request($apiHost);
            $request->requestTimeout = $this->requestTimeout;
            $request->body = $requestData;
            $request->verb = 'POST';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/vnd.cpc.shipment-v5+xml');
            $request->setHeader('Content-Type', 'application/vnd.cpc.shipment-v5+xml');
            $request->setHeader('Accept-language', static::ACCEPT_LANGUAGE_EN);

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (isset($response->body)
                && !empty($response->body)
            ) {
                // Parse response to object
                $result = $this->parseResponse($response->body);

            } else {
                $result->errors = array(
                    'INTERNAL' => sprintf('Error while connecting to the Canada Post host (%s) during Create Shipment request', $apiHost),
                );
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($apiHost, 'Create Shipment', $requestData, $response->body);
            }

        } catch (\Exception $e) {
            if (!isset($result->errors)) {
                $result->errors = array();
            }

            $result->errors += array($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    /**
     * Call Transmit Shipments request
     *
     * Reason to Call:
     * Used when one or more groups of shipments is ready for pickup by Canada Post or drop-off to a Canada Post location.
     * This can be used for shipments belonging to any customer who has a contract.
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return \XLite\Core\CommonCell
     */
    public function callTransmitShipmentsRequest(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $requestData = $this->getTransmitShipmentsXmlData($parcel);

        $apiHost = $this->getTransmitShipmentsEndpoint();

        $result = new \XLite\Core\CommonCell();

        try {
            $request = new \XLite\Core\HTTP\Request($apiHost);
            $request->requestTimeout = $this->requestTimeout;
            $request->body = $requestData;
            $request->verb = 'POST';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/vnd.cpc.manifest-v5+xml');
            $request->setHeader('Content-Type', 'application/vnd.cpc.manifest-v5+xml');
            $request->setHeader('Accept-language', static::ACCEPT_LANGUAGE_EN);

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (isset($response->body)
                && !empty($response->body)
            ) {
                // Parse response to object
                $result = $this->parseResponse($response->body);

            } else {
                $result->errors = array(
                    'INTERNAL' => sprintf('Error while connecting to the Canada Post host (%s) during Transmit Shipments request', $apiHost),
                );
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($apiHost, 'Transmit Shipment', $requestData, $response->body);
            }

        } catch (\Exception $e) {
            if (!isset($result->errors)) {
                $result->errors = array();
            }

            $result->errors += array($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    /**
     * Check parsed XML document for API error messages
     *
     * @param array $parsedXml Parsed XML document
     *
     * @return array
     */
    protected function parseResponseErrors($parsedXml)
    {
        $errors = array();

        if (isset($parsedXml['messages'])) {
            // Collect error messages and codes
            $messages = \XLite\Core\XML::getInstance()->getArrayByPath($parsedXml, 'messages/message');

            if (is_array($messages)
                && !empty($messages)
            ) {
                // Get errors from XML data
                foreach ($messages as $k => $v) {
                    $errors[] = $this->createErrorMessage(
                        \XLite\Core\XML::getInstance()->getArrayByPath($v, 'code/0/#'),
                        \XLite\Core\XML::getInstance()->getArrayByPath($v, 'description/0/#')
                    );
                }

            } else {
                // Unexpected error (when 'messages' element exists, but no 'message' elements were found)
                $errors[] = $this->createErrorMessage('UNEXP', 'An unexpected error occurred');
            }
        }

        return $errors;
    }

    /**
     * Create error message object
     *
     * @param string $code        Error code
     * @param string $description Error description
     *
     * @return \XLite\Core\CommonCell
     */
    protected function createErrorMessage($code, $description)
    {
        $message = new \XLite\Core\CommonCell();

        $message->code = (string) $code;
        $message->description = (string) $description;

        return $message;
    }

    /**
     * Parse response returned by the "Create Shipment", "Create Non-Contract Shipment", "Transmit Shipments" and "Get Manifest" requests
     *
     * @param string $respData Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponse($respData)
    {
        $result = new \XLite\Core\CommonCell();

        $xml = \XLite\Core\XML::getInstance();

        $xmlParsed = $xml->parse($respData, $err);

        if (isset($xmlParsed['messages'])) {
            // Collect errors

            $result->errors = array();

            $errors = $xml->getArrayByPath($xmlParsed, 'messages/message');

            foreach ($errors as $k => $v) {
                $result->errors += array($xml->getArrayByPath($v, 'code/0/#') => $xml->getArrayByPath($v, 'description/0/#'));
            }
        }

        if (isset($xmlParsed['manifests'])) {
            // Collect data for "Transmit Shipments" request

            $manifestLinksRaw = $xml->getArrayByPath($xmlParsed, 'manifests/link');

            $result->links = $this->parseResponseLinks($manifestLinksRaw);
        }

        if (isset($xmlParsed['manifest'])) {
            // Collect data for "Get Manifest" request

            $result->poNumber = $xml->getArrayByPath($xmlParsed, 'manifest/po-number/0/#');

            $linksRaw = $xml->getArrayByPath($xmlParsed, 'manifest/links/link');

            $result->links = $this->parseResponseLinks($linksRaw);
        }

        if (isset($xmlParsed['shipment-info'])
            || isset($xmlParsed['non-contract-shipment-info'])
        ) {
            // Collect data for "Create Shipment" and "Create Non-Contract Shipment" requests

            $shipmentInfo = null;

            if (isset($xmlParsed['shipment-info'])) {
                $shipmentInfo = $xml->getArrayByPath($xmlParsed, 'shipment-info');
                $result->returnType = 'shipment-info';
            }

            if (isset($xmlParsed['non-contract-shipment-info'])) {
                $shipmentInfo = $xml->getArrayByPath($xmlParsed, 'non-contract-shipment-info');
                $result->returnType = 'non-contract-shipment-info';
            }

            // A unique identifier for the shipment.
            if ($shipmentInfo['#']['shipment-id']) {
                $result->shipmentId = $xml->getArrayByPath($shipmentInfo, 'shipment-id/0/#');
            }

            if (isset($shipmentInfo['#']['shipment-status'])) {
                // Indicates the current status of the shipment. (Valid values are: "created", "transmitted", "suspended")
                $result->shipmentStatus = $xml->getArrayByPath($shipmentInfo, 'shipment-status/0/#');
            }

            if (isset($shipmentInfo['#']['tracking-pin'])) {
                // This is the tracking PIN for the shipment.
                $result->trackingPin = $xml->getArrayByPath($shipmentInfo, 'tracking-pin/0/#');
            }

            if (isset($shipmentInfo['#']['return-tracking-pin'])) {
                // This is the tracking PIN for the return shipment.
                $result->returnTrackingPin = $xml->getArrayByPath($shipmentInfo, 'return-tracking-pin/0/#');
            }

            if (isset($shipmentInfo['#']['po-number'])) {
                // The Canada Post Purchase Order number; only applicable and returned on a shipment where no manifest is required for proof of payment.
                $result->poNumber = $xml->getArrayByPath($shipmentInfo, 'po-number/0/#');
            }

            if (isset($shipmentInfo['#']['links'])) {
                // This structure represents a list of links to information relating to the shipment that was created.
                $linksRaw = $xml->getArrayByPath($shipmentInfo, 'links/link');

                $result->links = $this->parseResponseLinks($linksRaw);
            }
        }

        return $result;
    }

    /**
     * Parse response returned by the "Create Shipment", "Create Non-Contract Shipment", "Transmit Shipments" and "Get Manifest" requests
     *
     * @param array $links Links array
     *
     * @return array
     */
    protected function parseResponseLinks($links)
    {
        $_links = array();

        foreach ($links as $k => $v) {
            $link = new \XLite\Core\CommonCell();

            $link->rel = $v['@']['rel'];
            $link->href = $v['@']['href'];
            $link->mediaType = $v['@']['media-type'];

            if (isset($v['@']['index'])) {
                $link->idx = $v['@']['index'];
            }

            $_links[] = $link;
        }

        return $_links;
    }

    /**
     * Call Void Shipment request
     *
     * Reason to Call:
     * To delete a specific shipment prior to transmit.
     *
     * More info at:
     * https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/shippingmanifest/voidshipment.jsf
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return \XLite\Core\CommonCell
     */
    public function callVoidShipmentRequest(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $selfLink = $parcel->getShipment()->getLinkByRel(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Shipment\Link::REL_SELF);

        $apiHost = $selfLink->getHref();

        $result = new \XLite\Core\CommonCell();

        try {
            $request = new \XLite\Core\HTTP\Request($apiHost);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'DELETE';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', $selfLink->getMediaType());
            $request->setHeader('Accept-language', static::ACCEPT_LANGUAGE_EN);

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (isset($response)) {
                $errorCodes = array(
                    0    => 'Unknown error',
                    404  => 'The resource was not found so the shipment id is incorrect or the shipment has already been voided.',
                    8064 => 'The shipment has been transmitted and cannot be voided.',
                );

                if ($response->code == 204) {
                    // Valid response: do nothing

                } else {
                    $result->errors = array(
                        'VS_' . $response->code => (isset($errorCodes[$response->code])) ? $errorCodes[$response->code] : $errorCodes[0],
                    );
                }
            } else {
                $result->errors = array(
                    'INTERNAL' => sprintf('Error while connecting to the Canada Post host (%s) during Void Shipment request', $apiHost),
                );
            }

        } catch (\Exception $e) {
            if (!isset($result->errors)) {
                $result->errors = array();
            }

            $result->errors += array($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    /**
     * Call Get Artifact request
     *
     * Reason to Call:
     * To retrieve a shipping label, a return label, or the paperwork required for shipment pickup or drop-off (manifest).
     *
     * More info at:
     * https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/shippingmanifest/shipmentartifact.jsf
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Base\Link $link Shipment's link object
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetArtifactRequest(\XLite\Module\XC\CanadaPost\Model\Base\Link $link)
    {
        $apiHost = $link->getHref();

        $result = new \XLite\Core\CommonCell();

        try {
            $request = new \XLite\Core\HTTP\Request($apiHost);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'GET';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/pdf');
            $request->setHeader('Accept-language', static::ACCEPT_LANGUAGE_EN);

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (isset($response)
                && $response->code == 200
                && strpos($response->headers->ContentType, 'application/pdf') !== false
            ) {
                // Save valid PDF file to a temporary file
                $filePath = LC_DIR_TMP . $link->getFileName();

                if (\Includes\Utils\FileManager::write($filePath, $response->body)) {
                    $result->filePath = $filePath;
                }

            } elseif (!empty($response->body)
                && strpos($response->headers->ContentType, 'xml') > -1
            ) {
                // Parse errors
                $result = $this->parseResponse($response->body);

            } else {
                if (isset($response)
                    && $response->code == 202
                ) {
                    $result->errors = array(
                        'GAR_202' => 'The requested resource is not yet available. Please try again later.',
                    );

                } else {
                    // Other errors
                    $result->errors = array(
                        'GAR_01' => sprintf('Error while connecting to the Canada Post host (%s) during Get Artifact request', $apiHost),
                    );
                }
            }

        } catch (\Exception $e) {
            if (!isset($result->errors)) {
                $result->errors = array();
            }

            $result->errors += array($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    /**
     * Call "Get Manifest" request
     *
     * Reason to Call:
     * To retrieve the set of information links for a particular manifest that was previously created as part of Transmit Shipments.
     *
     * More info at:
     * https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/shippingmanifest/manifest.jsf
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest $manifest Manifest object
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetManifestRequest(\XLite\Module\XC\CanadaPost\Model\Order\Parcel\Manifest $manifest)
    {
        $apiHost = $manifest->getHref();

        $result = new \XLite\Core\CommonCell();

        try {
            $request = new \XLite\Core\HTTP\Request($apiHost);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'GET';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/vnd.cpc.manifest-v5+xml');
            $request->setHeader('Accept-language', static::ACCEPT_LANGUAGE_EN);

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (isset($response->body)
                && !empty($response->body)
            ) {
                // Parse response to object
                $result = $this->parseResponse($response->body);

            } else {
                $result->errors = array(
                    'INTERNAL' => sprintf('Error while connecting to the Canada Post host (%s) during Get Manifest request', $apiHost),
                );
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($apiHost, 'Get Manifest', '', $response->body);
            }

        } catch (\Exception $e) {
            if (!isset($result->errors)) {
                $result->errors = array();
            }

            $result->errors += array($e->getCode(), $e->getMessage());
        }

        return $result;
    }

    // }}}

    // {{{ Service functions to generate Canada Post API calls

    /**
     * Get XML data for Create Non-Contract Shipment request
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel
     *
     * @return string
     */
    protected function getNCShipmentXmlData(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $xmlHeader = '<' . '?xml version="1.0" encoding="utf-8"?' . '>';

        $request = <<<XML
{$xmlHeader}
<non-contract-shipment xmlns="http://www.canadapost.ca/ws/ncshipment">
    <delivery-spec>
        <service-code>{$parcel->getOrder()->getCapostShippingMethodCode()}</service-code>
{$this->getSenderXmlData($parcel, static::QUOTE_TYPE_NON_CONTRACTED)}
{$this->getDestinationXmlData($parcel->getOrder()->getProfile())}
{$this->getOptionsCommonXmlBlockByParcel($parcel)}
{$this->getParcelCharacteristicsXmlData($parcel, static::QUOTE_TYPE_NON_CONTRACTED)}
{$this->getNotificationCommonXmlBlockByParcel($parcel)}
{$this->getPreferencesCommonXmlBlock()}
{$this->getReferencesCommonXmlBlockByParcel($parcel)}
{$this->getCustomsCommonXmlBlockByParcel($parcel)}
    </delivery-spec>
</non-contract-shipment>
XML;

        return $request;
    }

    /**
     * Get XML data for "Create Shipment" request
     * TODO: make protected
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return string
     */
    public function getShipmentXmlData(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $contractId = static::getCanadaPostConfig()->contract_id;

        $request = <<<XML
<shipment xmlns="http://www.canadapost.ca/ws/shipment-v5">
    <group-id>{$parcel->getId()}</group-id>
{$this->getPickUpTypeXmlData()}
    <delivery-spec>
        <service-code>{$parcel->getOrder()->getCapostShippingMethodCode()}</service-code>
{$this->getSenderXmlData($parcel, static::QUOTE_TYPE_CONTRACTED)}
{$this->getDestinationXmlData($parcel->getOrder()->getProfile())}
{$this->getOptionsCommonXmlBlockByParcel($parcel)}
{$this->getParcelCharacteristicsXmlData($parcel, static::QUOTE_TYPE_CONTRACTED)}
{$this->getNotificationCommonXmlBlockByParcel($parcel)}
{$this->getPreferencesCommonXmlBlock()}
{$this->getReferencesCommonXmlBlockByParcel($parcel)}
{$this->getCustomsCommonXmlBlockByParcel($parcel)}
        <settlement-info>
            <contract-id>{$contractId}</contract-id>
            <intended-method-of-payment>Account</intended-method-of-payment>
        </settlement-info>
    </delivery-spec>
</shipment>
XML;

        $xmlHeader = '<' . '?xml version="1.0" encoding="utf-8"?' . '>';

        return $xmlHeader ."\n" . $request;
    }

    /**
     * Get XML data for "Transmit Shipments" request
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return string
     */
    protected function getTransmitShipmentsXmlData(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $xmlHeader = '<' . '?xml version="1.0" encoding="utf-8"?' . '>';

        $detailedManifests = static::getCanadaPostConfig()->detailed_manifests ? 'true' : 'false';

        $sourceAddress = $parcel->getOrder()->getSourceAddress();
        $stateCode = '';
        if ($sourceAddress->getState()) {
            $stateCode = $sourceAddress->getState()->getCode();
        }
        $zipcode = static::strToUpper(
            preg_replace('/\s+/', '', $sourceAddress->getZipcode())
        );

        $companyData = \XLite\Core\Config::getInstance()->Company;

        $manifestName = static::getCanadaPostConfig()->manifest_name;

        $request = <<<XML
{$xmlHeader}
<transmit-set xmlns="http://www.canadapost.ca/ws/manifest-v5">
    <group-ids>
        <group-id>{$parcel->getId()}</group-id>
    </group-ids>
{$this->getPickUpTypeXmlData()}
    <detailed-manifests>{$detailedManifests}</detailed-manifests>
    <method-of-payment>Account</method-of-payment>
    <manifest-address>
        <manifest-company>{$companyData->company_name}</manifest-company>

XML;

        if (!empty($manifestName)) {
            $request .= <<<XML
        <manifest-name>{$manifestName}</manifest-name>

XML;
        }

        $request .= <<<XML
        <phone-number>{$companyData->company_phone}</phone-number>
        <address-details>
            <address-line-1>{$sourceAddress->getStreet()}</address-line-1>
            <city>{$sourceAddress->getCity()}</city>
            <prov-state>{$stateCode}</prov-state>
            <country-code>{$sourceAddress->getCountryCode()}</country-code>
            <postal-zip-code>{$zipcode}</postal-zip-code>
        </address-details>
    </manifest-address>
</transmit-set>
XML;

        return $request;
    }

    /**
     * Get pick up type for "Create Shipment" and "Transmit Shipments" requests
     *
     * @return string
     */
    protected function getPickUpTypeXmlData()
    {
        if (static::PICKUP_TYPE_MANUAL == static::getCanadaPostConfig()->pick_up_type) {
            $siteNum = static::getCanadaPostConfig()->deposit_site_num;

            $xmlData = <<<XML
    <shipping-point-id>{$siteNum}</shipping-point-id>
XML;
        } else {
            $shippingPoint = preg_replace('/\s+/', '', \XLite\Core\Config::getInstance()->Company->origin_zipcode);

            $xmlData = <<<XML
    <cpc-pickup-indicator>true</cpc-pickup-indicator>
    <requested-shipping-point>{$shippingPoint}</requested-shipping-point>
XML;
        }

        return $xmlData;
    }

    /**
     * Get "sender" XML data for "Create Non-Contract Shipment" and "Create Shipment" requests
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     * @param string $quoteType Quote type
     *
     * @return string
     */
    protected function getSenderXmlData(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel, $quoteType = self::QUOTE_TYPE_NON_CONTRACTED)
    {
        $sourceAddress = $parcel->getOrder()->getSourceAddress();
        $stateCode = '';
        if ($sourceAddress->getState()) {
            $stateCode = $sourceAddress->getState()->getCode();
        }
        $zipcode = static::strToUpper(
            preg_replace('/\s+/', '', $sourceAddress->getZipcode())
        );

        $companyData = \XLite\Core\Config::getInstance()->Company;

        $xmlData = <<<XML
        <sender>
            <company>{$companyData->company_name}</company>
            <contact-phone>{$companyData->company_phone}</contact-phone>
            <address-details>
                <address-line-1>{$sourceAddress->getStreet()}</address-line-1>
                <city>{$sourceAddress->getCity()}</city>
                <prov-state>{$stateCode}</prov-state>
                <country-code>{$sourceAddress->getCountryCode()}</country-code>
                <postal-zip-code>{$zipcode}</postal-zip-code>
            </address-details>
        </sender>
XML;
        if (static::QUOTE_TYPE_NON_CONTRACTED == $quoteType) {
            // Remove country code for non-contracted request
            $xmlData = preg_replace('/<country-code>.*<\/country-code>/', '', $xmlData);
        }

        return $xmlData;
    }

    /**
     * Get "destination" XML data for "Create Non-Contract Shipment" and "Create Shipment" requests
     *
     * @param \XLite\Model\Profile $profile Customer profile
     *
     * @return string
     */
    protected function getDestinationXmlData(\XLite\Model\Profile $profile)
    {
        $destLocation = $profile->getShippingAddress();
        $destLocationZipcode = static::strToUpper(
            preg_replace('/\s+/', '', $destLocation->getZipcode())
        );

        $xmlData = <<<XML
        <destination>
            <name>{$destLocation->getFirstname()} {$destLocation->getLastname()}</name>
            <client-voice-number>{$destLocation->getPhone()}</client-voice-number>
            <address-details>
                <address-line-1>{$destLocation->getStreet()}</address-line-1>
                <city>{$destLocation->getCity()}</city>
                <prov-state>{$destLocation->getState()->getCode()}</prov-state>
                <country-code>{$destLocation->getCountry()->getCode()}</country-code>
                <postal-zip-code>{$destLocationZipcode}</postal-zip-code>
            </address-details>
        </destination>
XML;

        return $xmlData;
    }

    /**
     * Get "options" XML block
     * Common for "Create Non-Contract Shipment" and "Create Shipment" calls
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel object
     *
     * @return string
     */
    protected function getOptionsCommonXmlBlockByParcel(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $xmlData = '';

        $optClasses = $parcel->getAllowedOptionClasses();

        foreach ($optClasses as $optClass => $classData) {
            $value = $parcel->{'getOpt' . \XLite\Core\Converter::convertToCamelCase($optClass)}();

            if ($classData[$parcel::OPT_SCHEMA_MULTIPLE]
                && $classData[$parcel::OPT_SCHEMA_MANDATORY]
                && !isset($classData[$parcel::OPT_SCHEMA_ALLOWED_OPTIONS][$value])
            ) {
                // Set allowed option value
                $value = array_shift(array_keys($classData[$parcel::OPT_SCHEMA_ALLOWED_OPTIONS]));
            }

            if ($parcel::OPT_CLASS_SIGNATURE == $optClass) {
                $value = ($value)
                    ? array_shift(array_keys($classData[$parcel::OPT_SCHEMA_ALLOWED_OPTIONS]))
                    : '';
            }

            if ($parcel::OPT_CLASS_COVERAGE == $optClass) {
                if (0 >= $value) {
                    continue;
                }

                $coverage = static::applyConversionRate($value);
                $coverage = static::adjustFloatValue($coverage, 2, 0.01, 99999.99);

                $value = $parcel::OPT_COVERAGE;

                $xmlData .= <<<XML

            <option>
                <option-code>{$value}</option-code>
                <option-amount>{$coverage}</option-amount>
            </option>
XML;

            } elseif (!empty($value)) {
                $xmlData .= <<<XML

            <option>
                <option-code>{$value}</option-code>
            </option>
XML;
            }
        }

        if ($parcel->isDeliveryToPostOffice()) {
            // Add "D2PO" option if parcel should be delivered to the selected Canada Post post office
            $office = $parcel->getOrder()->getCapostOffice();

            $value = $parcel::OPT_DELIVER_TO_PO;

            $xmlData .= <<<XML

            <option>
                <option-code>{$value}</option-code>
                <option-qualifier-2>{$office->getOfficeId()}</option-qualifier-2>
            </option>
XML;
        }

        if (!empty($xmlData)) {
            $xmlData = <<<XML
        <options>
{$xmlData}
        </options>
XML;
        }

        return $xmlData;
    }

    /**
     * Get "parcel-characteristics" XML data for "Create Non-Contract Shipment" and "Create Shipment" requests
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel
     * @param string $quoteType Quote type
     *
     * @return string
     */
    protected function getParcelCharacteristicsXmlData(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel, $quoteType = self::QUOTE_TYPE_NON_CONTRACTED)
    {
        // Convert dimensions (dimensions must be in CM)
        $dimensions = array();

        foreach (array('length', 'width', 'height') as $v) {
            $dimensions[$v] = static::adjustFloatValue(
                $parcel->{'getBox' . \XLite\Core\Converter::convertToCamelCase($v)}(), 1, 0.1, 999.9
            );
        }

        $xmlData = <<<XML
            <weight>{$parcel->getWeightInKg(true)}</weight>
            <dimensions>
                <length>{$dimensions['length']}</length>
                <width>{$dimensions['width']}</width>
                <height>{$dimensions['height']}</height>
            </dimensions>
XML;

        // Parcel characteristic options
        $parcelTypes = array(
            'unpackaged',
            'mailing_tube',
        );

        if (static::QUOTE_TYPE_NON_CONTRACTED == $quoteType) {
            // Allowed only for "Non-Contracted" shipments
            $parcelTypes[] = 'document';

        } else {
            // Allowed only for "Contracted" shipments
            $parcelTypes[] = 'oversized';
        }

        foreach ($parcelTypes as $v) {
            if ($parcel->{'getIs' . \XLite\Core\Converter::convertToCamelCase($v)}()
            ) {
                $printTagName = str_replace('_', '-', $v);

                $xmlData .= <<<XML

            <{$printTagName}>true</{$printTagName}>
XML;
            }
        }

        $xmlData = <<<XML

        <parcel-characteristics>
{$xmlData}
        </parcel-characteristics>
XML;

        return $xmlData;
    }

    /**
     * Get "notification" XML block
     * Common for "Create Non-Contract Shipment" and "Create Shipment" calls
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel model
     *
     * @return string
     */
    protected function getNotificationCommonXmlBlockByParcel(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $notifications = array();

        foreach (array('shipment', 'exception', 'delivery') as $v) {
            $notifications[$v] = ($parcel->{'getNotifyOn' . \XLite\Core\Converter::convertToCamelCase($v)}()) ? 'true' : 'false';
        }

        if ($parcel->isDeliveryToPostOffice()) {
            // Notification "on shipment" is mandatory when parcel should be delivered to a Canada Post post office
            $notifications['shipment'] = 'true';
        }

        $xmlData = <<<XML
        <notification>
            <email>{$parcel->getOrder()->getProfile()->getLogin()}</email>
            <on-shipment>{$notifications['shipment']}</on-shipment>
            <on-exception>{$notifications['exception']}</on-exception>
            <on-delivery>{$notifications['delivery']}</on-delivery>
        </notification>
XML;

        return $xmlData;
    }

    /**
     * Get "preferences" XML block
     * Common for "Create Non-Contract Shipment" and "Create Shipment" calls
     *
     * @return string
     */
    protected function getPreferencesCommonXmlBlock()
    {
        $xmlData = <<<XML
        <preferences>
            <show-packing-instructions>true</show-packing-instructions>
        </preferences>
XML;

        return $xmlData;
    }

    /**
     * Get "references" XMl block
     * Common for "Create Non-Contract Shipment" and "Create Shipment" calls
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel model
     *
     * @return string
     */
    protected function getReferencesCommonXmlBlockByParcel(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $ref1 = $parcel->getOrder()->getOrderId();
        $ref2 = $parcel->getId();

        $xmlData = <<<XML
        <references>
            <customer-ref-1>{$ref1}</customer-ref-1>
            <customer-ref-2>{$ref2}</customer-ref-2>>
        </references>
XML;
        $xmlData = ''; // FIXME: v5 - does not support it
        return $xmlData;
    }

    /**
     * Get "customs" XMl block
     * Common for "Create Non-Contract Shipment" and "Create Shipment" calls
     *
     * @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel model
     *
     * @return string
     */
    protected function getCustomsCommonXmlBlockByParcel(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        // TODO: make 'reason-for-export' field as a selectable option (in settings or on shipments page)

        $xmlData = <<<XML
        <customs>
            <currency>CAD</currency>
            <reason-for-export>SOG</reason-for-export>
{$this->getSkuListCommonXmlBlockByParcel($parcel)}
        </customs>
XML;

        return $xmlData;
    }

    /**
     * Get "sku-list" XML block (part of the "customs" block)
     * Common for "Create Non-Contract Shipment" and "Create Shipment" calls
     *
     *  @param \XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel Canada Post parcel model
     *
     * @return string
     */
    protected function getSkuListCommonXmlBlockByParcel(\XLite\Module\XC\CanadaPost\Model\Order\Parcel $parcel)
    {
        $xmlDataItems = '';

        foreach ($parcel->getItems() as $item) {
            $itemDescr = static::adjustStringValue($item->getOrderItem()->getName(), 44);
            $itemSku = static::adjustStringValue($item->getOrderItem()->getSku(), 44);

            // Convert item price
            $itemPrice = static::applyConversionRate($item->getOrderItem()->getItemPrice());
            $itemPrice = static::adjustFloatValue($itemPrice, 2, 0.01, 99999.99);

            $xmlDataItems .= <<<XML
                <item>
                    <customs-number-of-units>{$item->getAmount()}</customs-number-of-units>
                    <customs-description>{$itemDescr}</customs-description>
                    <sku>{$itemSku}</sku>
                    <unit-weight>{$item->getWeightInKg(true)}</unit-weight>
                    <customs-value-per-unit>{$itemPrice}</customs-value-per-unit>
                </item>

XML;
        }

        $xmlData = <<<XML
            <sku-list>
{$xmlDataItems}
            </sku-list>
XML;
        return $xmlData;
    }

    // }}}

    // {{{ Common methods

    /**
     * Adjust float $value with $precision and within limits ($min and $max)
     *
     * @param float $value     Amount
     * @param int   $precision Precision
     * @param float $min       Min amount
     * @param float $max       Max amount
     *
     * @return float
     */
    public static function adjustFloatValue($value, $precision, $min, $max)
    {
        return min($max, max($min, \XLite\Logic\Math::getInstance()->round($value, $precision)));
    }

    /**
     * Adjust string $value within $max limit
     *
     * @param string  $value String
     * @param integer $max   Maximum string's length
     *
     * @return string
     */
    public static function adjustStringValue($value, $max)
    {
        $value = htmlspecialchars(htmlspecialchars_decode($value));

        $value =  ($max < static::getStringLength($value))
            ? static::subString($value, 0, $max)
            : $value;

        if (static::getStringLength($value) == $max) {
            //check if HTML entity(&[some_code];) is truncated - remove it
            if (preg_match('/(&[^;]++)(?!;)$/', $value)) {
                $value = static::subString($value, 0, $max - ($max - strrpos($value, '&') - 1));
            }
        }

        return $value;
    }

    /**
     * Wrapper for "substr" function to support UTF8 strings
     *
     * @return string
     */
    public static function subString()
    {
        $args = func_get_args();

        if (function_exists('mb_substr')) {
            if (!isset($args[3]) && isset($args[2])) {
                $args[3] = 'UTF-8';
            }

            $result = call_user_func_array('mb_substr', $args);

        } else {
            $result = call_user_func_array('substr', $args);
        }

        return $result;
    }

    /**
     * Wrapper for "strlen" function to support UTF8 strings
     *
     * @param string $str      String
     * @param string $encoding Encoding (OPTIONAL)
     *
     * @return string
     */
    public static function getStringLength($str, $encoding = 'UTF-8')
    {
        return (function_exists('mb_strlen'))
            ? mb_strlen($str, $encoding)
            : strlen($str);
    }

    /**
     * Wrapper for "strtoupper" function to support UTF8 strings
     *
     * @param string $str      String
     * @param string $encoding Encoding (OPTIONAL)
     *
     * @return string
     */
    public static function strToUpper($str, $encoding = 'UTF-8')
    {
        return (function_exists('mb_strtoupper'))
            ? mb_strtoupper($str, $encoding)
            : strtoupper($str);
    }

    /**
     * Get Canada Post settings
     *
     * @return \XLite\Core\ConfigCell
     */
    public static function getCanadaPostConfig()
    {
        if (null === static::$configuration) {
            static::$configuration = \XLite\Core\Config::getInstance()->XC->CanadaPost;
        }

        return static::$configuration;
    }

    /**
     * Get Canada Post settings
     *
     * @param \XLite\Core\ConfigCell $config Config
     *
     * @return void
     */
    public static function setCanadaPostConfig($config)
    {
        static::$configuration = $config;
    }

    /**
     * Save API call to the log file
     *
     * @param string $url          API endpoint
     * @param string $callName     Call name
     * @param string $requestData  Request XML
     * @param string $responseData Response XML
     *
     * @return void
     */
    public static function logApiCall($url, $callName, $requestData, $responseData)
    {
        \XLite\Logger::logCustom(
            'CAPOST',
            var_export(
                array(
                    'Request URL' => $url,
                    'Request XML (' . $callName . ')' => $requestData,
                    'Response XML' => \XLite\Core\XML::getInstance()->getFormattedXML($responseData),
                ),
                true
            )
        );
    }

    /**
     * Get currency conversion rate
     *
     * @return float
     */
    public static function getCurrencyConversionRate()
    {
        $rate = doubleval(static::getCanadaPostConfig()->currency_rate);

        return $rate ?: 1;
    }

    /**
     * Apply conversion rate to an amount
     *
     * @param float $amount Amount
     *
     * @return float
     */
    public static function applyConversionRate($amount)
    {
        return round($amount / static::getCurrencyConversionRate(), 2);
    }

    /**
     * Check - is request should be made on behalf of a merchant
     *
     * @return boolean
     */
    public static function isOnBehalfOfAMerchant()
    {
        $wizardHash = static::getCanadaPostConfig()->wizard_hash;

        $result = false;

        if (!empty($wizardHash)
            && $wizardHash == md5(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password)
        ) {
            $result = true;
        }

        return $result;
    }

    /**
     * Convert XML files name to camel case
     *
     * @param string $field XML field name
     *
     * @return string
     */
    public static function convertXmlFieldToCamelCase($field)
    {
        return lcfirst(\XLite\Core\Converter::convertToCamelCase(str_replace('-', '_', $field)));
    }

    /**
     * Convert XML elements to an object
     *
     * @param array $parsedXml Parsed XML data
     *
     * @return \XLite\Core\CommonCell
     */
    public static function convertXmlTags($parsedXml)
    {
        $elements = new \XLite\Core\CommonCell();

        foreach ($parsedXml['#'] as $field => $value) {
            $field = static::convertXmlFieldToCamelCase($field);
            $value = $value[0]['#'];

            if (!is_array($value)) {
                $elements->{$field} = trim($value);
            }
        }

        return $elements;
    }

    /**
     * Get allowed for "Delivery to Post Office" shipping methods codes
     *
     * @return array
     */
    public static function getAllowedForDelivetyToPOMethodCodes()
    {
        return array('DOM.EP', 'USA.EP', 'DOM.XP', 'DOM.XP.CERT', 'USA.XP', 'INT.XP');
    }

    // }}}

    // {{{ Cache control methods

    /**
     * Get key hash
     *
     * @param string $key Hash key
     *
     * @return string
     */
    protected function getKeyHash($key)
    {
        return 'CAPOST_API_' . md5($key);
    }

    /**
     * Get saved data from cache
     *
     * @param string $key Key of a cache cell
     *
     * @return mixed
     */
    protected function getDataFromCache($key)
    {
        $data = null;
        $cacheDriver = \XLite\Core\Database::getCacheDriver();
        $key = $this->getKeyHash($key);

        if ($cacheDriver->contains($key)) {
            $data = $cacheDriver->fetch($key);
        }

        return $data;
    }

    /**
     * Save data into the cache
     *
     * @param string $key  Key of a cache cell
     * @param mixed  $data Data object for saving in the cache
     *
     * @return void
     */
    protected function saveDataInCache($key, $data)
    {
        \XLite\Core\Database::getCacheDriver()->save($this->getKeyHash($key), $data);
    }

    // }}}
}
