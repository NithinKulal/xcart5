<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\Model\Shipping\API;

use XLite\Module\XC\UPS\Model\Shipping\Mapper;
use XLite\Module\XC\UPS\Model\Shipping\Processor;

class UPS
{
    /**
     * @var Processor\UPS
     */
    protected $processor;

    /**
     * @param Processor\UPS $processor
     */
    public function __construct($processor)
    {
        $this->processor = $processor;
    }

    /**
     * Returns API endpoint
     *
     * @return string
     */
    protected function getApiURL()
    {
        return $this->processor->isTestMode()
            ? 'https://wwwcie.ups.com/ups.app/xml'
            : 'https://onlinetools.ups.com:443/ups.app/xml';
    }

    /**
     * @param array $inputData
     *
     * @return mixed
     */
    public function getRates($inputData)
    {
        $url = $this->getApiURL() . '/Rate';

        $request = new Request\XMLRequest($url, $inputData);
        $request->setInputMapper(new Mapper\Rate\InputMapper($this->processor));
        $request->setOutputMapper(new Mapper\Rate\OutputMapper($this->processor));
        $request->sendRequest();

        $this->processor->addApiCommunicationMessage(
            array(
                'method' => __METHOD__,
                'URL' => $url,
                'request' => $request->getRawRequest(),
                'response' => $request->getRawResponse(),
            )
        );

        $this->processor->log(
            array(
                'method' => __METHOD__,
                'URL' => $url,
                'request' => $request->getRawRequest(),
                'response' => $request->getRawResponse(),
            )
        );

        return $request->getResponse();
    }

    /**
     * @param array $inputData
     *
     * @return mixed
     */
    public function getRatesCOD($inputData)
    {
        $url = $this->getApiURL() . '/Rate';

        $request = new Request\XMLRequest($url, $inputData);
        $request->setInputMapper(new Mapper\RateCOD\InputMapper($this->processor));
        $request->setOutputMapper(new Mapper\RateCOD\OutputMapper($this->processor));
        $request->sendRequest();

        $this->processor->addApiCommunicationMessage(
            array(
                'method' => __METHOD__,
                'URL' => $url,
                'request' => $request->getRawRequest(),
                'response' => $request->getRawResponse(),
            )
        );

        $this->processor->log(
            array(
                'method' => __METHOD__,
                'URL' => $url,
                'request' => \XLite\Core\XML::getInstance()->getFormattedXML($request->getRawRequest()),
                'response' => \XLite\Core\XML::getInstance()->getFormattedXML($request->getRawResponse()),
            )
        );

        return $request->getResponse();
    }
}
