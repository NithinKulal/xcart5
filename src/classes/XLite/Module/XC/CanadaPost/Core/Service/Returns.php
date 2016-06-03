<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\CanadaPost\Core\Service;

/**
 * Implementation of the Canada Post's "Returns" service
 *
 * Service Summary:
 *
 *   The returns web service allows you to create shipping labels for authorized and open returns. 
 *   Both generate "bill on scan" return labels that are not associated with a manifest and do not need to be associated with an outgoing label.
 *
 * More info at:
 *
 *   https://www.canadapost.ca/cpo/mc/business/productsservices/developers/services/returns/default.jsf
 *
 * Implemented calls:
 *
 *   Create Authorized Return
 *   Get Artifact (common call, implemented in Contract Shipping)
 *
 */
class Returns extends \XLite\Module\XC\CanadaPost\Core\Service\AService
{
    // {{{ Endpoints

    /**
     * Canada Post "Create Authorized Return" URL template
     *
     * @var string
     */
    protected $createAuthorizedReturnEndpoint = 'https://XX/rs/{mailed by customer}/{mobo}/authorizedreturn';

    /**
     * Get "Create Authorized Return" request endpoint
     *
     * @return string
     */
    public function getCreateAuthorizedReturnEndpoint()
    {
        return $this->prepareEndpoint($this->createAuthorizedReturnEndpoint);
    }

    // }}}

    // {{{ "Create Authorized Return" call 
    
    /**
     * Call "Create Authorized Return" request by the ProductsReturn model
     *
     * @param \XLite\Module\XC\CanadaPost\Model\ProductsReturn $return Canada Post return model
     *
     * @return \XLite\Core\CommonCell
     */
    public function callCreateAuthorizedReturnByProductsReturn(\XLite\Module\XC\CanadaPost\Model\ProductsReturn $return)
    {
        $xmlHeader = '<' . '?xml version="1.0" encoding="utf-8"?' . '>';

        // Convert weight into KG
        $itemsWeight = \XLite\Core\Converter::convertWeightUnits(
            $return->getItemsTotalWeight(),
            \XLite\Core\Config::getInstance()->Units->weight_unit,
            'kg'
        );

        $itemsWeight = static::adjustFloatValue($itemsWeight, 3, 0.1, 999.999);

        $requestData = <<<XML
{$xmlHeader}
<authorized-return xmlns="http://www.canadapost.ca/ws/authreturn-v2">
    <service-code>{$return->getOrder()->getCapostShippingMethodCode()}</service-code>
{$this->getXmlBlockReturnerAddress($return->getOrder()->getProfile())}
{$this->getXmlBlockReceiverAddress()}
    <parcel-characteristics>
        <weight>{$itemsWeight}</weight>
    </parcel-characteristics>
    <print-preferences>
        <output-format>8.5x11</output-format>
        <encoding>PDF</encoding>
    </print-preferences>
    <settlement-info></settlement-info>
</authorized-return>
XML;
        
        return $this->callCreateAuthorizedReturn($requestData);
    }

    /**
     * Call "Create Authorized Return" request
     *
     * Reason to Call:
     * To create an authorized return that allows you to retrieve and print a return shipping label that can be sent (either physically or electronically) to a shopper.
     *
     * @param string $requestData Request XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function callCreateAuthorizedReturn($requestData)
    {
        $apiHost = $this->getCreateAuthorizedReturnEndpoint();

        $result = new \XLite\Core\CommonCell();

        try {

            $request = new \XLite\Core\HTTP\Request($apiHost);
            $request->requestTimeout = $this->requestTimeout;
            $request->body = $requestData;
            $request->verb = 'POST';
            $request->setHeader('Authorization', 'Basic ' . base64_encode(static::getCanadaPostConfig()->user . ':' . static::getCanadaPostConfig()->password));
            $request->setHeader('Accept', 'application/vnd.cpc.authreturn-v2+xml');
            $request->setHeader('Content-Type', 'application/vnd.cpc.authreturn-v2+xml');
            $request->setHeader('Accept-language', $this->getAcceptLanguage());

            if (static::isOnBehalfOfAMerchant()) {
                $request->setHeader('Platform-id', $this->getPlatformId());
            }
    
            $response = $request->sendRequest();

            if (
                isset($response->body)
                && !empty($response->body)
            ) {
                // Parse XML response to object
                $result = $this->parseResponseCreateAuthorizedReturn($response->body);

            } else {
                
                // Register request error
                $result->errors = array(
                    $this->createErrorMessage(
                        'INTERNAL', 
                        sprintf('Error while connecting to the Canada Post host (%s) during "Create Authorized Return" request', $apiHost)
                    )
                );
            }

            if (static::getCanadaPostConfig()->debug_enabled) {
                // Save debug log
                static::logApiCall($apiHost, 'Create Authorized Return', $requestData, $response->body);
            }
       
        } catch (\Exception $e) {
            
            // Register exception error
            $errorMessage = $this->createErrorMessage($this->getCode(), $this->getMessage());
            
            $result->errors = array_merge((array) $result->errors, array($errorMessage));
        }

        return $result;
    }

    /**
     * Parcel response of the "Create Authorized Return" call
     *
     * @param string $responseXml Response XML data
     *
     * @return \XLite\Core\CommonCell
     */
    protected function parseResponseCreateAuthorizedReturn($responseXml)
    {
        $result = new \XLite\Core\CommonCell();

        // Parse XML document
        $xml = \XLite\Core\XML::getInstance();
 
        $xmlParsed = $xml->parse($responseXml, $err);

        if (isset($xmlParsed['messages'])) {

            // Collect API error messages (using common method)
            $result->errors = $this->parseResponseErrors($xmlParsed);

        } else if (isset($xmlParsed['authorized-return-info'])) {
        
            // Collect returned data from "Create Authorized Return" call
            $data = new \XLite\Core\CommonCell();
            
            $data->trackingPin = $xml->getArrayByPath($xmlParsed, 'authorized-return-info/tracking-pin/0/#');

            $data->links = $this->parseResponseLinks(
                $xml->getArrayByPath($xmlParsed, 'authorized-return-info/links/link')
            );

            $result->authorizedReturnInfo = $data;
        }

        return $result;
    }

    /**
     * Get XML block: returner (sender) address 
     *
     * @param \XLite\Model\Profile $profile Returner profile
     *
     * @return string
     */
    protected function getXmlBlockReturnerAddress(\XLite\Model\Profile $profile)
    {
        $address = $profile->getShippingAddress();
        $addressZipcode = preg_replace('/\s+/', '', $address->getZipcode());
        
        $xmlData = <<<XML
    <returner>
        <name>{$address->getFirstname()} {$address->getLastname()}</name>
        <domestic-address>
            <address-line-1>{$address->getStreet()}</address-line-1>
            <city>{$address->getCity()}</city>
            <province>{$address->getState()->getCode()}</province>
            <postal-code>{$addressZipcode}</postal-code>
        </domestic-address>
    </returner>
XML;
        
        return $xmlData;
    }

    /**
     * Get XML block: receiver address
     *
     * @return string
     */
    protected function getXmlBlockReceiverAddress()
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
    <receiver>
        <name>{$companyData->company_name}</name>
        <company>{$companyData->company_name}</company>
        <domestic-address>
            <address-line-1>{$sourceAddress->getStreet()}</address-line-1>
            <city>{$sourceAddress->getCity()}</city>
            <province>{$stateCode}</province>
            <postal-code>{$zipcode}</postal-code>
        </domestic-address>
    </receiver>
XML;
        
        return $xmlData;
    }

    // }}}
}
