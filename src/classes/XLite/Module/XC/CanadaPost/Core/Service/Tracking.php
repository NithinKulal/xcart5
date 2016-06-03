<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 
namespace XLite\Module\XC\CanadaPost\Core\Service;

/**
 * Implementation of the Canada Post's "Tracking" service
 *
 * Service Summary:
 *
 *   Tracking services allow you to retrieve information about a parcel's progress through the mail
 *   stream and to get details and artifacts related to delivery results.
 *
 * More info at:
 *
 *   https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/tracking/default.jsf
 *
 * Implemented calls:
 *
 *   Get Tracking Details
 *   Get Signature Image
 *   Get Delivery Confirmation Certificate
 *
 */
class Tracking extends \XLite\Module\XC\CanadaPost\Core\Service\AService
{
    // {{{ Endpoints

    /**
     * Canada Post "Get Tracking Details" URL template (by PIN number)
     *
     * @var string
     */
    protected $getTrackingDetailsPinEndpoint = 'https://XX/vis/track/pin/{pin number}/detail';

    /**
     * Canada Post "Get Tracking Details" URL template (by DNC number)
     *
     * @var string
     */
    protected $getTrackingDetailsDncEndpoint = 'https://XX/vis/track/dnc/{dnc number}/detail';

    /**
     * Canada Post "Get Signature Image" URL template
     *
     * @var string
     */
    protected $getSignatureImageEndpoint = 'https://XX/vis/signatureimage/{pin number}';

    /**
     * Canada Post "Get Delivery Confirmation Certificate" URL template
     *
     * @var string
     */
    protected $getDeliveryConfirmationCertificateEndpoint = 'https://XX/ot/certificate/{pin number}';

    /**
     * Get "Get Tracking Details" request endpoint (by PIN number)
     *
     * @param string $pinNumber PIN number
     *
     * @return string
     */
    public function getGetTrackingDetailsPinEndpoint($pinNumber)
    {
        $endpoint = $this->prepareEndpoint($this->getTrackingDetailsPinEndpoint);

        return str_replace('{pin number}', $pinNumber, $endpoint);
    }

    /**
     * Get "Get Tracking Details" request endpoint (by DNC number)
     *
     * @param string $dncNumber DNC number
     *
     * @return string
     */
    public function getGetTrackingDetailsDncEndpoint($dncNumber)
    {
        $endpoint = $this->prepareEndpoint($this->getTrackingDetailsDncEndpoint);

        return str_replace('{dnc number}', $dncNumber, $endpoint);
    }

    /**
     * Get "Get Signature Image" request endpoint
     *
     * @param string $pin PIN number
     *
     * @return string
     */
    public function getGetSignatureImageEndpoint($pin)
    {
        $endpoint = $this->prepareEndpoint($this->getSignatureImageEndpoint);

        return str_replace('{pin number}', $pin , $endpoint);
    }

    /**
     * Get "Get Delivery Confirmation Certificate" request endpoint
     *
     * @param string $pin PIN number
     *
     * @return string
     */
    public function getGetDeliveryConfirmCertEndpoint($pin)
    {
        $endpoint = $this->prepareEndpoint($this->getDeliveryConfirmationCertificateEndpoint);

        return str_replace('{pin number}', $pin, $endpoint);
    }

    // }}}

    // {{{ "Get Tracking Details" call

    /**
     * Call "Get Post Office Detail" request by the PIN number
     *
     * @param string $pinNumber PIN Number
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetTrackingDetailsByPinNumber($pinNumber)
    {
        return $this->callGetTrackingDetails(
            $this->getGetTrackingDetailsPinEndpoint($pinNumber)
        );
    }

    /**
     * Call "Get Post Office Detail" request by the DNC number
     *
     * @param string $dncNumber DNC Number
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetTrackingDetailsByDncNumber($dncNumber)
    {
        return $this->callGetTrackingDetails(
            $this->getGetTrackingDetailsDncEndpoint($dncNumber)
        );
    }

    /**
     * Call "Get Tracking Details" request
     *
     * Reason to Call:
     * To get all tracking events for a single parcel
     *
     * @param string $endpoint Service endpoint (URL)
     *
     * @return \XLite\Core\CommonCell
     */
    protected function callGetTrackingDetails($endpoint)
    {
        $result = new \XLite\Core\CommonCell();

        try {

            $request = new \XLite\Core\HTTP\Request($endpoint);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'GET';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/vnd.cpc.track+xml');
            $request->setHeader('Accept-language', $this->getAcceptLanguage());

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (
                isset($response->body)
                && !empty($response->body)
            ) {
                // Parse response to object
                $result = $this->parseResponseGetTrackingDetails($response->body);

            } else {

                // Register request error
                $errorMessage = $this->createErrorMessage(
                    'INTERNAL',
                    sprintf(
                        'Error while connecting to the Canada Post host (%s) during "Get Tracking Details" request',
                        $endpoint
                    )
                );

                $result->errors = array($errorMessage);
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($endpoint, 'Get Tracking Details', '', $response->body);
            }

        } catch (\Exception $e) {

            // Register exception error
            $errorMessage = $this->createErrorMessage($this->getCode(), $this->getMessage());

            $result->errors = array_merge((array) $result->errors, array($errorMessage));
        }

        return $result;
    }

    /**
     * Parse response of the "Get Tracking Details" call
     *
     * @param string $responseXml Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponseGetTrackingDetails($responseXml)
    {
        $result = new \XLite\Core\CommonCell();

        // Parse XML document
        $xml = \XLite\Module\XC\CanadaPost\Core\XML::getInstance();

        $err = null;

        $xmlParsed = $xml->parse($responseXml, $err);

        if (isset($xmlParsed['messages'])) {

            // Collect API error messages (using common method)
            $result->errors = $this->parseResponseErrors($xmlParsed);

        } else if (isset($xmlParsed['tracking-detail'])) {

            // Collect returned data from "Get Tracking Details" call
            $result->trackingDetail = $xml::convertParsedXmlDocument($xmlParsed['tracking-detail']);

            // Correct "delivery-options" data
            $deliveryOptionsRaw = $xml->getArrayByPath($xmlParsed, 'tracking-detail/delivery-options/item');

            $deliveryOptions = array();

            if (
                !empty($deliveryOptionsRaw)
                && is_array($deliveryOptionsRaw)
            ) {
                foreach ($deliveryOptionsRaw as $item) {

                    if (!empty($item['#']['delivery-option'][0]['#'])) {

                        $deliveryOptions[$item['#']['delivery-option'][0]['#']] = trim(
                            $item['#']['delivery-option-description'][0]['#']
                        );
                    }
                }

            }

            $result->trackingDetail->deliveryOptions = $deliveryOptions;

            // Correct "significant-events" data
            $significantEventsRaw = $xml->getArrayByPath($xmlParsed, 'tracking-detail/significant-events/occurrence');

            $significantEvents = array();

            if (
                !empty($significantEventsRaw)
                && is_array($significantEventsRaw)
            ) {
                foreach ($significantEventsRaw as $event) {

                    $_event = $xml::convertParsedXmlDocument($event);

                    $_event->eventDescription = trim(
                        preg_replace('/\s+/', ' ', $_event->eventDescription)
                    );

                    $significantEvents[] = $_event;
                }
            }

            $result->trackingDetail->significantEvents = $significantEvents;
        }

        return $result;
    }

    // }}}

    // {{{ "Get Signature Image" call

    /**
     * Call "Get Signature Image" request by the PIN number
     *
     * @param string $pinNumber PIN Number
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetSignatureImageByPinNumber($pinNumber)
    {
        return $this->callGetSignatureImage(
            $this->getGetSignatureImageEndpoint($pinNumber)
        );
    }

    /**
     * Call "Get Signature Image" request
     *
     * Reason to Call:
     * To retrieve the image of the signature provided for a specific parcel
     *
     * @param string $endpoint Service endpoint (URL)
     *
     * @return \XLite\Core\CommonCell
     */
    protected function callGetSignatureImage($endpoint)
    {
        $result = new \XLite\Core\CommonCell();

        try {

            $request = new \XLite\Core\HTTP\Request($endpoint);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'GET';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/vnd.cpc.track+xml');
            $request->setHeader('Accept-language', $this->getAcceptLanguage());

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (
                isset($response->body)
                && !empty($response->body)
            ) {
                // Parse response to object
                $result = $this->parseResponseGetSignatureImage($response->body);

            } else {

                // Register request error
                $errorMessage = $this->createErrorMessage(
                    'INTERNAL',
                    sprintf(
                        'Error while connecting to the Canada Post host (%s) during "Get Signature Image" request',
                        $endpoint
                    )
                );

                $result->errors = array($errorMessage);
            }

        } catch (\Exception $e) {

            // Register exception error
            $errorMessage = $this->createErrorMessage($this->getCode(), $this->getMessage());

            $result->errors = array_merge((array) $result->errors, array($errorMessage));
        }

        return $result;
    }

    /**
     * Parse response of the "Get Signature Image" call
     *
     * @param string $responseXml Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponseGetSignatureImage($responseXml)
    {
        $result = new \XLite\Core\CommonCell();

        // Parse XML document
        $xml = \XLite\Module\XC\CanadaPost\Core\XML::getInstance();

        $err = null;

        $xmlParsed = $xml->parse($responseXml, $err);

        if (isset($xmlParsed['messages'])) {

            // Collect API error messages (using common method)
            $result->errors = $this->parseResponseErrors($xmlParsed);

        } else if (isset($xmlParsed['signature-image'])) {

            // Collect returned data from "Get Signature Image" call
            $result->signatureImage = $xml::convertParsedXmlDocument($xmlParsed['signature-image']);
        }

        return $result;
    }

    // }}}

    // {{{ "Get Delivery Confirmation Certificate" call

    /**
     * Call "Get Delivery Confirmation Certificate" request by the PIN number
     *
     * @param string $pinNumber PIN Number
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetDeliveryConfirmCertByPinNumber($pinNumber)
    {
        return $this->callGetDeliveryConfirmCert(
            $this->getGetDeliveryConfirmCertEndpoint($pinNumber)
        );
    }

    /**
     * Call "Get Delivery Confirmation Certificate" request
     *
     * Reason to Call:
     * To retrieve the image of the delivery confirmation certificate (which is a document showing proof
     * of delivery with details) for a given parcel
     *
     * @param string $endpoint Service endpoint (URL)
     *
     * @return \XLite\Core\CommonCell
     */
    protected function callGetDeliveryConfirmCert($endpoint)
    {
        $result = new \XLite\Core\CommonCell();

        try {

            $request = new \XLite\Core\HTTP\Request($endpoint);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'GET';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/vnd.cpc.track+xml');
            $request->setHeader('Accept-language', $this->getAcceptLanguage());

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }

            $response = $request->sendRequest();

            if (
                isset($response->body)
                && !empty($response->body)
            ) {
                // Parse response to object
                $result = $this->parseResponseGetDeliveryConfirmCert($response->body);

            } else {

                // Register request error
                $errorMessage = $this->createErrorMessage(
                    'INTERNAL',
                    sprintf(
                        'Error while connecting to the Canada Post host (%s) during "Get Delivery Confirmation Certificate" request',
                        $endpoint
                    )
                );

                $result->errors = array($errorMessage);
            }

        } catch (\Exception $e) {

            // Register exception error
            $errorMessage = $this->createErrorMessage($this->getCode(), $this->getMessage());

            $result->errors = array_merge((array) $result->errors, array($errorMessage));
        }

        return $result;
    }

    /**
     * Parse response of the "Get Delivery Confirmation Certificate" call
     *
     * @param string $responseXml Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponseGetDeliveryConfirmCert($responseXml)
    {
        $result = new \XLite\Core\CommonCell();

        // Parse XML document
        $xml = \XLite\Module\XC\CanadaPost\Core\XML::getInstance();

        $err = null;

        $xmlParsed = $xml->parse($responseXml, $err);

        if (isset($xmlParsed['messages'])) {

            // Collect API error messages (using common method)
            $result->errors = $this->parseResponseErrors($xmlParsed);

        } else if (isset($xmlParsed['delivery-confirmation-certificate'])) {

            // Collect returned data from "Get Delivery Confirmation Certificate" call
            $result->deliveryConfirmationCertificate = $xml::convertParsedXmlDocument(
                $xmlParsed['delivery-confirmation-certificate']
            );
        }

        return $result;
    }

    // }}}
}
