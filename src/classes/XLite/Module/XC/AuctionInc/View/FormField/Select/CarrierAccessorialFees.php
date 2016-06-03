<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\View\FormField\Select;

/**
 * Carrier accessorial fees selector
 */
class CarrierAccessorialFees extends \XLite\View\FormField\Select\Multiple
{
    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'AddlHandling'         => 'Apply additional handling charges to ship rates of all supporting carriers',
            'AddlHandlingUPS'      => 'Apply additional handling charges to UPS ship rates',
            'AddlHandlingDHL'      => 'Apply additional handling charges to DHL ship rates',
            'AddlHandlingFDX'      => 'Apply additional handling charges to Fedex ship rates',
            'Hazard'               => 'Indicate that this package qualifies as hazardous content for all carriers',
            'HazardUPS'            => 'Indicate that this package qualifies as hazardous content for UPS',
            'HazardDHL'            => 'Indicate that this package qualifies as hazardous content for DHL',
            'HazardFDX'            => 'Indicate that this package qualifies as hazardous content for Fedex',
            'SignatureReq'         => 'Apply additional charge for signature required for all supporting carriers',
            'SignatureReqUPS'      => 'Apply additional charge for signature required for UPS',
            'SignatureReqDHL'      => 'Apply additional charge for signature required for DHL',
            'SignatureReqFDX'      => 'Apply additional charge for (indirect) signature required for FedEx',
            'SignatureReqUSP'      => 'Apply additional charge for signature required for USPS',
            'UPSAdultSignature'    => 'Apply additional charge for adult signature required for UPS',
            'DHLAdultSignature'    => 'Apply additional charge for adult signature required for DHL',
            'FDXAdultSignature'    => 'Apply additional charge for adult signature required for FedEx',
            'DHLPrefSignature'     => 'Apply additional charge for signature preferred for DHL',
            'FDXDirectSignature'   => 'Apply additional charge for direct signature required for FedEx',
            'FDXHomeCertain'       => 'Apply additional charge for home date certain delivery for FedEx Home Delivery (FDXHD) carrier service',
            'FDXHomeEvening'       => 'Apply additional charge for home evening delivery for FedEx Home Delivery (FDXHD) carrier service',
            'FDXHomeAppmnt'        => 'Apply additional charge for home appointment delivery for FedEx Home Delivery (FDXHD) carrier service',
            'Pod'                  => 'Apply additional charge for proof of delivery for all supporting carriers',
            'PodUPS'               => 'Apply additional charge for proof of delivery for UPS',
            'PodDHL'               => 'Apply additional charge for proof of delivery for DHL',
            'PodFDX'               => 'Apply additional charge for proof of delivery for FedEx',
            'PodUSP'               => 'Apply additional charge for proof of delivery for USPS',
            'UPSDelivery'          => 'Apply additional charge for delivery confirmation for UPS',
            'USPCertified'         => 'Apply additional charge for certified delivery for USPS',
            'USPRestricted'        => 'Apply additional charge for restricted delivery for USPS',
            'USPDelivery'          => 'Apply additional charge for delivery confirmation for USPS',
            'USPReturn'            => 'Apply additional charge for return receipt for USPS',
            'USPReturnMerchandise' => 'Apply additional charge for return receipt for merchandise for USPS',
            'USPRegistered'        => 'Apply additional charge for registered mail for USPS',
            'IrregularUSP'         => 'Indicate that this package qualifies for the USPS dimensional weighting discount for irregularly-shaped items',
        );
    }
}
