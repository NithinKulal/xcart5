<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\UPS\Model\Shipping\Mapper\Rate;

use XLite\Core;
use XLite\Module\XC\UPS\Model\Shipping;

class InputMapper extends Shipping\Mapper\AMapper
{
    protected $weightUnit;
    protected $dimensionUnit;

    protected $destinationCurrency;

    /**
     * Is mapper able to map?
     *
     * @return boolean
     */
    protected function isApplicable()
    {
        return $this->inputData && is_array($this->inputData);
    }

    /**
     * Perform actual mapping
     *
     * @return mixed
     */
    protected function performMap()
    {
        $result = '';

        $srcAddress = $this->inputData['srcAddress'];
        if (in_array($srcAddress['country'], array('DO', 'PR', 'US', 'CA'), true)) {
            $this->weightUnit = 'LBS';
            $this->dimensionUnit = 'IN';

        } else {
            $this->weightUnit = 'KGS';
            $this->dimensionUnit = 'CM';
        }

        $config = $this->getConfiguration();
        $dstAddress = $this->inputData['dstAddress'];
        $dstCountry = Core\Database::getRepo('XLite\Model\Country')
            ->findOneBy(array('code' => $dstAddress['country']));
        $this->destinationCurrency = $dstCountry && $dstCountry->getCurrency()
            ? $dstCountry->getCurrency()->getCode()
            : $config->currency_code;

        $result .= $this->getAccessRequest();
        $result .= $this->getRatingServiceSelectionRequest();

        return $result;
    }

    /**
     * @return string
     */
    protected function getAccessRequest()
    {
        $config = $this->getConfiguration();

        $result =<<<XML
<?xml version='1.0'?>
<AccessRequest xml:lang='en-US'>
    <AccessLicenseNumber>{$config->accessKey}</AccessLicenseNumber>
    <UserId>{$config->userID}</UserId>
    <Password>{$config->password}</Password>
</AccessRequest>
XML;

        return $result;
    }

    /**
     * @return string
     */
    protected function getRatingServiceSelectionRequest()
    {
        $config = $this->getConfiguration();
        $shipment = $this->getShipment();

        $result =<<<XML
<?xml version='1.0'?>
<RatingServiceSelectionRequest>
    <Request>
        <RequestAction>Rate</RequestAction>
        <RequestOption>Shop</RequestOption>
    </Request>
    <PickupType>
        <Code>{$config->pickup_type}</Code>
    </PickupType>
    {$shipment}
</RatingServiceSelectionRequest>
XML;

        return $result;
    }

    /**
     * @return string
     */
    protected function getShipment()
    {
        $shipper = $this->getShipper();
        $shipTo = $this->getShipTo();
        $shipFrom = $this->getShipFrom();
        $packages = $this->getPackages();

        $shipmentServiceOptions = '';
        $serviceOptions = $this->getShipmentServiceOptions();
        if ($serviceOptions) {
            $serviceOptionsStr = implode($serviceOptions);
            $shipmentServiceOptions =<<<XML
<ShipmentServiceOptions>
    {$serviceOptionsStr}
</ShipmentServiceOptions>
XML;
        }

        $rateInformation = $this->getRateInformation();

        return <<<XML
<Shipment>
    {$shipper}
    {$shipTo}
    {$shipFrom}
    {$packages}
    {$shipmentServiceOptions}
    {$rateInformation}
</Shipment>
XML;
    }

    /**
     * @return string
     */
    protected function getShipper()
    {
        $config = $this->getConfiguration();

        $shipperNumber = $config->shipper_number
            ? '<ShipperNumber>' . $config->shipper_number . '</ShipperNumber>'
            : '';

        $srcAddress = $this->inputData['srcAddress'];

        $result =<<<XML
<Shipper>
    {$shipperNumber}
    <Address>
        <City>{$srcAddress['city']}</City>
        <StateProvinceCode>{$srcAddress['state']}</StateProvinceCode>
        <PostalCode>{$srcAddress['zipcode']}</PostalCode>
        <CountryCode>{$srcAddress['country']}</CountryCode>
    </Address>
</Shipper>
XML;

        return $result;
    }

    /**
     * @return string
     */
    protected function getShipTo()
    {
        $dstAddress = $this->inputData['dstAddress'];
        $residentialAddressIndicator = isset($dstAddress['type']) && 'R' === $dstAddress['type']
            ? '<ResidentialAddressIndicator />'
            : '';

        $result =<<<XML
<ShipTo>
    <Address>
        <City>{$dstAddress['city']}</City>
        <StateProvinceCode>{$dstAddress['state']}</StateProvinceCode>
        <PostalCode>{$dstAddress['zipcode']}</PostalCode>
        <CountryCode>{$dstAddress['country']}</CountryCode>
        {$residentialAddressIndicator}
    </Address>
</ShipTo>
XML;

        return $result;
    }

    /**
     * @return string
     */
    protected function getShipFrom()
    {
        $srcAddress = $this->inputData['srcAddress'];

        $result =<<<XML
<ShipFrom>
    <Address>
        <City>{$srcAddress['city']}</City>
        <StateProvinceCode>{$srcAddress['state']}</StateProvinceCode>
        <PostalCode>{$srcAddress['zipcode']}</PostalCode>
        <CountryCode>{$srcAddress['country']}</CountryCode>
    </Address>
</ShipFrom>
XML;

        return $result;
    }

    /**
     * @return string
     */
    protected function getPackages()
    {
        $result = array();
        foreach ($this->inputData['packages'] as $package) {
            $result[] = $this->getPackage($package);
        }

        return implode($result);
    }

    /**
     * @param array $package
     *
     * @return string
     */
    protected function getPackage($package)
    {
        $config = $this->getConfiguration();

        $dimensions = $this->getPackageDimensions($package);

        $packageServiceOptions = '';
        $serviceOptions = $this->getPackageServiceOptions($package);
        if ($serviceOptions) {
            $serviceOptionsStr = implode($serviceOptions);
            $packageServiceOptions =<<<XML
<PackageServiceOptions>
    {$serviceOptionsStr}
</PackageServiceOptions>
XML;
        }

        $additionalHandling = '';
        if ($config->additional_handling) {
            $additionalHandling =<<<XML
<AdditionalHandling />
XML;
        }

        $result =<<<XML
<Package>
    <PackagingType>
        <Code>{$config->packaging_type}</Code>
    </PackagingType>
    <PackageWeight>
        <UnitOfMeasurement>
            <Code>{$this->weightUnit}</Code>
        </UnitOfMeasurement>
        <Weight>{$package['weight']}</Weight>
    </PackageWeight>
    {$dimensions}
    {$packageServiceOptions}
    {$additionalHandling}
</Package>
XML;

        return $result;
    }

    /**
     * @param array $package
     *
     * @return string
     */
    protected function getPackageDimensions($package)
    {
        $result = '';
        $config = $this->getConfiguration();

        if (isset($package['box'])) {
            $length = $package['box']['length'];
            $width = $package['box']['width'];
            $height = $package['box']['height'];

        } else {
            list($length, $width, $height) = $config->dimensions;
        }

        if ($length + $width + $height > 0) {
            $length = round($length, 2);
            $width  = round($width, 2);
            $height = round($height, 2);

            $girth = $length + (2 * $width) + (2 * $height);
            $largePackageIndicator = $girth > 165 ? '<LargePackageIndicator />' : '';

            $result = <<<XML
<Dimensions>
    <UnitOfMeasurement>
        <Code>{$this->dimensionUnit}</Code>
    </UnitOfMeasurement>
    <Length>{$length}</Length>
    <Width>{$width}</Width>
    <Height>{$height}</Height>
</Dimensions>
{$largePackageIndicator}
XML;
        }

        return $result;
    }

    /**
     * @param array $package
     *
     * @return array
     */
    protected function getPackageServiceOptions($package)
    {
        $result = array();
        $config = $this->getConfiguration();
        $packageSubtotal = round((float) $package['subtotal'], 2);

        if ($config->extra_cover) {
            $insuredValue = round((float) $config->extra_cover_value, 2) ?: $packageSubtotal;
            if ($insuredValue > 0.1) {
                $result['InsuredValue'] =<<<XML
<InsuredValue>
    <CurrencyCode>{$config->currency_code}</CurrencyCode>
    <MonetaryValue>$insuredValue</MonetaryValue>
</InsuredValue>
XML;
            }
        }

        $DCISType = (int) $config->delivery_conf;
        $srcCountry = $this->inputData['srcAddress']['country'];
        $dstCountry = $this->inputData['dstAddress']['country'];
        if ($DCISType > 0
            && $DCISType < 4
            && 'US' === $srcCountry
            && 'US' === $dstCountry
        ) {
            $result['DeliveryConfirmation'] =<<<XML
<DeliveryConfirmation>
    <DCISType>$DCISType</DCISType>
</DeliveryConfirmation>
XML;
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getShipmentServiceOptions()
    {
        $result = array();
        $config = $this->getConfiguration();

        if ($config->saturday_pickup) {
            $result['SaturdayPickupIndicator'] =<<<XML
<SaturdayPickupIndicator />
XML;
        }

        if ($config->saturday_delivery) {
            $result['SaturdayPickupIndicator'] =<<<XML
<SaturdayDeliveryIndicator />
XML;
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getRateInformation()
    {
        $result = '';
        $config = $this->getConfiguration();

        if ($config->negotiated_rates) {
            $result = <<<XML
<RateInformation>
    <NegotiatedRatesIndicator />
</RateInformation>
XML;
        }

        return $result;
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
