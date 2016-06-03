<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Core\Service;

/**
 * Implementation of the Canada Post's "E-commerce Platforms" service
 *
 * Service Summary:
 *
 *   Use the platform web services if you are an e-commerce platform and want to register your merchant customers
 *   with Canada Post so that they can ship with Canada Post from your platform.
 *
 * More info at:
 *
 *   https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/ecomplatforms/default.jsf
 *
 * Implemented calls:
 *
 *   Get Merchant Registration Token
 *   Get Merchant Registration Info
 *
 */
class Platforms extends \XLite\Module\XC\CanadaPost\Core\Service\AService
{
    /**
     * Merchant registration statuses
     */
    const REG_STATUS_SUCCESS             = 'SUCCESS';
    const REG_STATUS_CANCELLED           = 'CANCELLED';
    const REG_STATUS_BAD_REQUEST         = 'BAD_REQUEST';
    const REG_STATUS_UNEXPECTED_ERROR    = 'UNEXPECTED_ERROR';
    const REG_STATUS_UNAUTHORIZED        = 'UNAUTHORIZED';
    const REG_STATUS_SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';

    // {{{ Endpoints

    /**
     * Canada Post "Get Merchant Registration Token" URL template
     *
     * @var string
     */
    protected $getMerchantRegTokenEndpoint = 'https://XX/ot/token';

    /**
     * Canada Post "Get Merchant Registration Info" URL template
     *
     * @var string
     */
    protected $getMerchantRegInfoEndpoint = 'https://XX/ot/token/{token-id}';
    /**
     * Get "Get Merchant Registration Token" request endpoint
     *
     * @return string
     */
    public function getGetMerchantRegTokenEndpoint()
    {
        return $this->prepareEndpoint($this->getMerchantRegTokenEndpoint);
    }

    /**
     * Get "Get Merchant Registration Info" request endpoint
     *
     * @param string $token Token
     *
     * @return string
     */
    public function getGetMerchantRegInfoEndpoint($token)
    {
        $endpoint = $this->prepareEndpoint($this->getMerchantRegInfoEndpoint);

        return str_replace('{token-id}', $token, $endpoint);
    }

    // }}}

    // {{{ "Get Merchant Registration Token" call

    /**
     * Call "Get Merchant Registration Token" request
     *
     * Reason to Call:
     * To get a unique registration token that is used to launch a merchant into the Canada Post sign-up process.
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetMerchantRegistrationToken()
    {
        $endpoint = $this->getGetMerchantRegTokenEndpoint();

        $capostAPIKey = $this->getCapostAPIkey(static::getCanadaPostConfig()->developer_mode);

        $result = new \XLite\Core\CommonCell();

        try {

            $request = new \XLite\Core\HTTP\Request($endpoint);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'POST';
            $request->body = ' ';
            $request->setHeader('Authorization', 'Basic ' . base64_encode($capostAPIKey->user . ':' . $capostAPIKey->password));
            $request->setHeader('Accept', 'application/vnd.cpc.registration+xml');
            $request->setHeader('Content-Type', 'application/vnd.cpc.registration+xml');
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
                $result = $this->parseResponseGetMerchantRegistrationToken($response->body);

            } else {

                // Register request error
                $errorMessage = $this->createErrorMessage(
                    'INTERNAL',
                    sprintf(
                        'Error while connecting to the Canada Post host (%s) during "Get Merchant Registration Token" request',
                        $endpoint
                    )
                );

                $result->errors = array($errorMessage);
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($endpoint, 'Get Merchant Registration Token', '', $response->body);
            }

        } catch (\Exception $e) {

            // Register exception error
            $errorMessage = $this->createErrorMessage($this->getCode(), $this->getMessage());

            $result->errors = array_merge((array) $result->errors, array($errorMessage));
        }

        return $result;
    }

    /**
     * Parse response of the "Get Merchant Registration Token" call
     *
     * @param string $responseXml Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponseGetMerchantRegistrationToken($responseXml)
    {
        $result = new \XLite\Core\CommonCell();

        // Parse XML document
        $xml = \XLite\Module\XC\CanadaPost\Core\XML::getInstance();

        $err = null;

        $xmlParsed = $xml->parse($responseXml, $err);

        if (isset($xmlParsed['messages'])) {

            // Collect API error messages (using common method)
            $result->errors = $this->parseResponseErrors($xmlParsed);

        } else if (isset($xmlParsed['token'])) {

            // Collect returned data from "Get Merchant Registration Token" call
            $result->token = $xml::convertParsedXmlDocument($xmlParsed['token']);
        }

        return $result;
    }

    // }}}

    // {{{ "Get Merchant Registration Info" call

    /**
     * Call "Get Merchant Registration Info" request by the token ID
     *
     * @param string $token Token ID
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetMerchantRegistrationInfoByToken($token)
    {
        $endpoint = $this->getGetMerchantRegInfoEndpoint($token);

        return $this->callGetMerchantRegistrationInfo($endpoint);
    }

    /**
     * Call "Get Merchant Registration Info" request
     *
     * Reason to Call:
     * Called by the e-commerce platform after the merchant has completed the Canada Post sign-up process
     *
     * @param string $endpoint Service endpoint (URL)
     *
     * @return \XLite\Core\CommonCell
     */
    protected function callGetMerchantRegistrationInfo($endpoint)
    {
        $capostAPIKey = $this->getCapostAPIkey(static::getCanadaPostConfig()->developer_mode);

        $result = new \XLite\Core\CommonCell();

        try {

            $request = new \XLite\Core\HTTP\Request($endpoint);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'GET';
            $request->setHeader('Authorization', 'Basic ' . base64_encode($capostAPIKey->user . ':' . $capostAPIKey->password));
            $request->setHeader('Accept', 'application/vnd.cpc.registration+xml');
            $request->setHeader('Content-Type', 'application/vnd.cpc.registration+xml');
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
                $result = $this->parseResponseGetMerchantRegistrationInfo($response->body);

            } else {

                // Register request error
                $errorMessage = $this->createErrorMessage(
                    'INTERNAL',
                    sprintf(
                        'Error while connecting to the Canada Post host (%s) during "Get Merchant Registration Info" request',
                        $endpoint
                    )
                );

                $result->errors = array($errorMessage);
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($endpoint, 'Get Merchant Registration Info', '', $response->body);
            }

        } catch (\Exception $e) {

            // Register exception error
            $errorMessage = $this->createErrorMessage($this->getCode(), $this->getMessage());

            $result->errors = array_merge((array) $result->errors, array($errorMessage));
        }

        return $result;
    }

    /**
     * Parse response of the "Get Merchant Registration Info" call
     *
     * @param string $responseXml Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponseGetMerchantRegistrationInfo($responseXml)
    {
        $result = new \XLite\Core\CommonCell();

        // Parse XML document
        $xml = \XLite\Module\XC\CanadaPost\Core\XML::getInstance();

        $err = null;

        $xmlParsed = $xml->parse($responseXml, $err);

        if (isset($xmlParsed['messages'])) {

            // Collect API error messages (using common method)
            $result->errors = $this->parseResponseErrors($xmlParsed);

        } else if (isset($xmlParsed['merchant-info'])) {

            // Collect returned data from "Get Merchant Registration Info" call
            $result->merchantInfo = $xml::convertParsedXmlDocument($xmlParsed['merchant-info']);
        }

        return $result;
    }

    // }}}
}
