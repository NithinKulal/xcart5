<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
 
namespace XLite\Module\XC\CanadaPost\Core\Service;

/**
 * Implementation of the Canada Post's "Find a Post Office" service
 *
 * Service Summary:
 *
 *   Find a Post Office allows you to get a list of Post Offices near a given
 *   location as well as details about each one.
 *
 * More info at:
 *
 *   https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/findpostoffice/default.jsf
 *
 * Implemented calls:
 *
 *   Get Nearest Post Office
 *   Get Post Office Detail
 *
 */
class PostOffice extends \XLite\Module\XC\CanadaPost\Core\Service\AService
{
    // {{{ Endpoints

    /**
     * Canada Post "Get Nearest Post Office" URL template
     *
     * @var string
     */
    protected $getGetNearestPostOfficeEndpoint = 'https://XX/rs/postoffice';

    /**
     * Get "Get Nearest Post Office" request endpoint
     *
     * @param array $data URL parameters
     *
     * @return string
     */
    public function getGetNearestPostOfficeEndpoint($data)
    {
        $endpoint = $this->prepareEndpoint($this->getGetNearestPostOfficeEndpoint);

        if (
            !empty($data)
            && is_array($data)
        ) {
            $allowed_params = array(
                'd2po', 'maximum', 'longitude', 'latitude', 'postalCode', 'province', 'city', 'streetName'
            );

            $params = array();

            foreach ($allowed_params as $param) {
                if (isset($data[$param])) {
                    $params[] = $param . '=' . $data[$param];
                }
            }

            if (!empty($params)) {
                $endpoint .= '?' . implode('&', $params);
            }
        }

        return $endpoint;
    }

    // }}}

    // {{{ "Get Nearest Post Office" call

    /**
     * Retrieve information about nearest Post Offices to a given location
     *
     * @param string  $zipCode     ZIP code (postal code)
     * @param boolean $d2po        Flag - search offices that only available for "Delivery to Post Office" (OPTIONAL)
     * @param integer $maxItems    Maximum number of the post offices to return (OPTIONAL)
     * @param boolean $ignoreCache Flag - ignore cache or not (OPTIONAL)
     *
     * @return array|null
     */
    public function callGetNearestPostOfficeByZipCode($zipCode, $d2po = false, $maxItems = null, $ignoreCache = false)
    {
        $zipCode = preg_replace('/\s+/', '', $zipCode);

        $params = array(
            'd2po'       => ($d2po) ? 'true' : 'false',
            'maximum'    => (isset($maxItems)) ? $maxItems : static::getCanadaPostConfig()->max_post_offices,
            'postalCode' => $zipCode,
        );

        $paramsHash = serialize($params);

        // Get data from cache (if enabled)
        $data = ($ignoreCache) ? null : $this->getDataFromCache($paramsHash);

        if (!isset($data)) {
            // Get data directly from Canada Post server
            $data = $this->callGetNearestPostOffice($params);
        }

        $offices = null;

        if (isset($data->postOfficeList)) {

            if (!$ignoreCache) {
                // Save data into the cache
                $this->saveDataInCache($paramsHash, $data);
            }

            $offices = array();

            foreach ($data->postOfficeList as $postOffice) {

                $office = new \XLite\Module\XC\CanadaPost\Model\PostOffice();

                // Set general details
                $office->setId($postOffice->officeId);
                $office->setName($postOffice->name);
                $office->setLocation($postOffice->location);
                $office->setDistance($postOffice->distance);
                $office->setBilingualDesignation(($postOffice->bilingualDesignation == 'true') ? true : false);
                $office->setLinkHref($postOffice->link->attrs->href);
                $office->setLinkMediaType($postOffice->link->attrs->mediaType);

                // Set address details
                foreach ($postOffice->address as $k => $v) {
                    $office->{'set' . ucfirst($k)}($v);
                }

                $offices[] = $office;
            }
        }

        return $offices;
    }

    /**
     * Call "Get Nearest Post Office" request
     *
     * Reason to Call:
     * To retrieve information on Post Offices nearest to a given location
     * To retrieve a list of Post Offices that support the option for direct delivery to a Post Office
     *
     * @param array $params URL parameters list
     *
     * @return \XLite\Core\CommonCell
     */
    protected function callGetNearestPostOffice($params)
    {
        if (!isset($params['maximum'])) {
            // User default maximum number of Post Offices to return
            $params['maximum'] = static::getCanadaPostConfig()->max_post_offices;
        }

        $endpoint = $this->getGetNearestPostOfficeEndpoint($params);

        $result = new \XLite\Core\CommonCell();

        try {

            $request = new \XLite\Core\HTTP\Request($endpoint);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'GET';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/vnd.cpc.postoffice+xml');
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
                $result = $this->parseResponseGetNearestPostOffice($response->body);

            } else {

                // Register request error
                $errorMessage = $this->createErrorMessage(
                    'INTERNAL',
                    sprintf(
                        'Error while connecting to the Canada Post host (%s) during "Get Nearest Post Office" request',
                        $endpoint
                    )
                );

                $result->errors = array($errorMessage);
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($endpoint, 'Get Nearest Post Office', '', $response->body);
            }

        } catch (\Exception $e) {

            // Register exception error
            $errorMessage = $this->createErrorMessage($this->getCode(), $this->getMessage());

            $result->errors = array_merge((array) $result->errors, array($errorMessage));
        }

        return $result;
    }

    /**
     * Parse response of the "Get Nearest Post Office" call
     *
     * @param string $responseXml Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponseGetNearestPostOffice($responseXml)
    {
        $result = new \XLite\Core\CommonCell();

        // Parse XML document
        $xml = \XLite\Module\XC\CanadaPost\Core\XML::getInstance();

        $err = null;

        $xmlParsed = $xml->parse($responseXml, $err);

        if (isset($xmlParsed['messages'])) {

            // Collect API error messages (using common method)
            $result->errors = $this->parseResponseErrors($xmlParsed);

        } else if (isset($xmlParsed['post-office-list'])) {

            // Collect returned data from "Get Nearest Post Office" call
            $offices = array();

            foreach ($xmlParsed['post-office-list']['#']['post-office'] as $office) {
                $offices[] = $xml::convertParsedXmlDocument($office);
            }

            $result->postOfficeList = $offices;
        }

        return $result;
    }

    // }}}

    // {{{ "Get Post Office Detail" call

    /**
     * Call "Get Post Office Detail" request by the provided endpoint (URL)
     *
     * @param string $endpoint     Service endpoint (URL)
     * @param string $acceptHeader Accept header value
     *
     * @return \XLite\Core\CommonCell
     */
    public function callGetPostOfficeDetailByEndpoint($endpoint, $acceptHeader = 'application/vnd.cpc.postoffice+xml')
    {
        return $this->callGetPostOfficeDetail($endpoint, $acceptHeader);
    }

    /**
     * Call "Get Post Office Detail" request
     *
     * Reason to Call:
     * To retrieve additional information about a specific Post Office
     *
     * @param string $endpoint     URL template
     * @param string $acceptHeader Accept header value
     *
     * @return \XLite\Core\CommonCell
     */
    protected function callGetPostOfficeDetail($endpoint, $acceptHeader)
    {
        $result = new \XLite\Core\CommonCell();

        try {

            $request = new \XLite\Core\HTTP\Request($endpoint);
            $request->requestTimeout = $this->requestTimeout;
            $request->verb = 'GET';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', $acceptHeader);
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
                $result = $this->parseResponseGetPostOfficeDetail($response->body);

            } else {

                // Register request error
                $errorMessage = $this->createErrorMessage(
                    'INTERNAL',
                    sprintf(
                        'Error while connecting to the Canada Post host (%s) during "Get Post Office Detail" request',
                        $endpoint
                    )
                );

                $result->errors = array($errorMessage);
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($endpoint, 'Get Post Office Detail', '', $response->body);
            }

        } catch (\Exception $e) {

            // Register exception error
            $errorMessage = $this->createErrorMessage($this->getCode(), $this->getMessage());

            $result->errors = array_merge((array) $result->errors, array($errorMessage));
        }

        return $result;
    }

    /**
     * Parse response of the "Get Post Office Detail" call
     *
     * @param string $responseXml Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponseGetPostOfficeDetail($responseXml)
    {
        $result = new \XLite\Core\CommonCell();

        // Parse XML document
        $xml = \XLite\Module\XC\CanadaPost\Core\XML::getInstance();

        $err = null;

        $xmlParsed = $xml->parse($responseXml, $err);

        if (isset($xmlParsed['messages'])) {

            // Collect API error messages (using common method)
            $result->errors = $this->parseResponseErrors($xmlParsed);

        } else if (isset($xmlParsed['post-office-detail'])) {

            // Collect returned data from "Get Post Office Detail" call
            $result->postOfficeDetail = $xml::convertParsedXmlDocument($xmlParsed['post-office-detail']);

            // Correct hours list
            $hoursList = array();

            foreach ($xmlParsed['post-office-detail']['#']['hours-list'] as $hours) {

                $_hours = new \XLite\Core\CommonCell();
                $_hours->day = $hours['#']['day'][0]['#'];

                $_time = array();

                foreach ($hours['#']['time'] as $time) {
                    $_time[] = $time['#'];
                }

                $_hours->time = $_time;

                $hoursList[] = $_hours;
            }

            $result->postOfficeDetail->hoursList = $hoursList;
        }

        return $result;
    }

    // }}}
}
