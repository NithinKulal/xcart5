<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\Model\Shipping\Mapper\Rate;

use XLite\Core;
use XLite\Model\Shipping\Rate;
use XLite\Module\XC\UPS;
use XLite\Module\XC\UPS\Model\Shipping;

/**
 * Output mapper
 */
class OutputMapper extends Shipping\Mapper\AMapper
{
    /**
     * @var \SimpleXMLElement
     */
    protected $parsed;

    /**
     * @var float
     */
    protected $currencyRate;

    /**
     * This table provides correct service codes for different origins
     * <ServiceCode returned from UPS> => array (<origin> => <code of shipping method>)
     *
     * @var array
     */
    protected static $upsServices = array(
        '01' => array(
            'US' => 'NDA',
            'CA' => 'EXP',
            'PR' => 'NDA',
        ),
        '02' => array(
            'US' => '2DA',
            'CA' => 'WEXDSM',
            'PR' => '2DA',
        ),
        '03' => array(
            'US' => 'GND',
            'PR' => 'GND',
        ),
        '07' => array(
            'US' => 'WEXPSM',
            'EU' => 'EXP',
            'CA' => 'EXP',
            'PL' => 'EXP',
            'PR' => 'WEXPSM',
            'MX' => 'EXP',
            'OTHER_ORIGINS' => 'EXP',
        ),
        '08' => array(
            'US' => 'WEXDSM',
            'EU' => 'EXDSM',
            'PL' => 'EXDSM',
            'PR' => 'WEXDSM',
            'MX' => 'EXDSM',
            'OTHER_ORIGINS' => 'WEXDSM',
        ),
        '11' => array(
            'US' => 'STD',
            'EU' => 'STD',
            'CA' => 'STD',
            'MX' => 'STD',
            'PL' => 'STD',
            'OTHER_ORIGINS' => 'STD',
        ),
        '12' => array(
            'US' => '3DS',
            'CA' => '3DS',
        ),
        '13' => array(
            'US' => 'NDAS',
            'CA' => 'SAVSM',
        ),
        '14' => array(
            'US' => 'NDAEAMSM',
            'CA' => 'EXPEAMSM',
            'PR' => 'NDAEAMSM',
        ),
        '54' => array(
            'US' => 'WEXPPSM',
            'EU' => 'WEXPPSM',
            'PL' => 'WEXPPSM',
            'PR' => 'WEXPPSM',
            'MX' => 'EXPP',
            'OTHER_ORIGINS' => 'WEXPPSM',
        ),
        '59' => array(
            'US' => '2DAAM',
        ),
        '65' => array(
            'US' => 'SAV',
            'EU' => 'SAV',
            'PL' => 'SAV',
            'PR' => 'SAV',
            'MX' => 'SAV',
            'OTHER_ORIGINS' => 'SAV',
        ),
        '82' => array(
            'PL' => 'TSTD',
        ),
        '83' => array(
            'PL' => 'TDC',
        ),
        '84' => array(
            'PL' => 'TI',
        ),
        '85' => array(
            'PL' => 'TEXP',
        ),
        '86' => array(
            'PL' => 'TEXPS',
        ),
        '96' => array(
            'US' => 'WEXPF',
            'EU' => 'WEXPF',
            'CA' => 'WEXPF',
            'PL' => 'WEXPF',
            'PR' => 'WEXPF',
            'MX' => 'WEXPF',
            'OTHER_ORIGINS' => 'WEXPF',
        ),
    );

    /**
     * @param string $serviceCode
     * @param string $sourceOriginCode
     *
     * @return string|null
     */
    protected static function getShippingServiceCode($serviceCode, $sourceOriginCode)
    {
        return isset(static::$upsServices[$serviceCode][$sourceOriginCode])
            ? static::$upsServices[$serviceCode][$sourceOriginCode]
            : null;
    }

    /**
     * @param UPS\Model\Shipping\Processor\UPS $processor Shipping processor
     */
    public function __construct(UPS\Model\Shipping\Processor\UPS $processor)
    {
        parent::__construct($processor);

        $this->currencyRate = (float) ($this->getConfiguration()->currency_rate ?: 1);

        libxml_use_internal_errors(true);
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        $parsed = $this->getParsed();
        if ($parsed->Response->Error) {
            return sprintf(
                'Error: %s - %s - %s',
                (string) $parsed->Response->Error->ErrorCode,
                (string) $parsed->Response->Error->ErrorSeverity,
                (string) $parsed->Response->Error->ErrorDescription
            );
        }

        return null;
    }

    /**
     * Is mapper able to map?
     *
     * @return boolean
     */
    protected function isApplicable()
    {
        return $this->inputData instanceof \PEAR2\HTTP\Request\Response
            && $this->getAdditionalData('request');
    }

    /**
     * Perform actual mapping
     *
     * @return Rate[]|null
     */
    protected function performMap()
    {
        $result = array();

        if ($this->isValid()) {
            foreach ($this->getParsed()->RatedShipment as $ratedShipment) {
                $rate = $this->getRate($ratedShipment);
                if ($rate) {
                    $result[] = $rate;
                }
            }
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @param \SimpleXMLElement $ratedShipment
     *
     * @return Rate|null
     */
    protected function getRate($ratedShipment)
    {
        $result = null;

        $method = $this->getMethod($ratedShipment);
        if ($method) {
            $result = new Rate();
            $result->setBaseRate($this->getBaseRate($ratedShipment));
            $result->setMethod($method);

            $extraData = new Core\CommonCell();
            if ($ratedShipment->GuaranteedDaysToDelivery) {
                $extraData->deliveryDays = (string) $ratedShipment->GuaranteedDaysToDelivery;
            }

            if ($extraData->getData()) {
                $result->setExtraData($extraData);
            }
        }

        return $result;
    }

    /**
     * @param \SimpleXMLElement $ratedShipment
     *
     * @return null|\XLite\Model\Shipping\Method
     */
    protected function getMethod($ratedShipment)
    {
        $requestData = $this->getAdditionalData('request');
        $sourceOriginCode = static::getOriginCode($requestData['srcAddress']['country']);
        $code = static::getShippingServiceCode((string) $ratedShipment->Service->Code, $sourceOriginCode);

        return $code
            ? $this->processor->getMethodByCode($code)
            : null;
    }

    /**
     * @param \SimpleXMLElement $ratedShipment
     *
     * @return float
     */
    protected function getBaseRate($ratedShipment)
    {
        if ($ratedShipment->NegotiatedRates) {
            $result = (float) $ratedShipment->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue;

        } else {
            $result = (float) $ratedShipment->TotalCharges->MonetaryValue;
        }

        return $result * $this->currencyRate;
    }

    /**
     * @return \SimpleXMLElement
     */
    protected function getParsed()
    {
        if (null === $this->parsed) {
            $this->parsed = simplexml_load_string($this->inputData);
        }

        return $this->parsed;
    }

    /**
     * @return boolean
     */
    protected function isValid()
    {
        return null === $this->getError();
    }

    /**
     * Post-process mapped data
     *
     * @param mixed $mapped mapped data to post-process
     *
     * @return mixed
     */
    protected function postProcessMapped($mapped)
    {
        return $mapped;
    }
}
