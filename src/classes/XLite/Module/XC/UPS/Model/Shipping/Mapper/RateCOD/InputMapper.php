<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\Model\Shipping\Mapper\RateCOD;

use XLite\Module\XC\UPS\Model\Shipping\Mapper\Rate;

class InputMapper extends Rate\InputMapper
{
    /**
     * @return array
     */
    protected function getShipmentServiceOptions()
    {
        $result = parent::getShipmentServiceOptions();

        $srcCountry = $this->inputData['srcAddress']['country'];
        $dstCountry = $this->inputData['dstAddress']['country'];
        if ($this->isShipmentCODAllowed($srcCountry, $dstCountry)) {
            $shipmentTotal = round((float) $this->inputData['total'], 2);

            $result['COD'] =<<<XML
<COD>
    <CODFundsCode>9</CODFundsCode>
    <CODAmount>
        <CurrencyCode>{$this->destinationCurrency}</CurrencyCode>
        <MonetaryValue>{$shipmentTotal}</MonetaryValue>
    </CODAmount>
</COD>
XML;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getPackageServiceOptions($package)
    {
        $result = parent::getPackageServiceOptions($package);
        $packageSubtotal = round((float) $package['subtotal'], 2);

        $srcCountry = $this->inputData['srcAddress']['country'];
        $dstCountry = $this->inputData['dstAddress']['country'];
        if ($this->isPackageCODAllowed($srcCountry, $dstCountry)) {
            unset($result['DeliveryConfirmation']);
            $result['COD'] =<<<XML
<COD>
    <CODFundsCode>0</CODFundsCode>
    <CODAmount>
        <CurrencyCode>{$this->destinationCurrency}</CurrencyCode>
        <MonetaryValue>{$packageSubtotal}</MonetaryValue>
    </CODAmount>
</COD>
XML;
        }

        return $result;
    }
}
