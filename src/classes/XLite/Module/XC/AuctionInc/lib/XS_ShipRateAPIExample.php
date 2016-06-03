<?php
/*
 *
 * Copyright (c) Paid, Inc.  All rights reserved.
 *
 * $Id: XS_ShipRateAPIExample.php,v 1.7 2015/03/08 17:39:48 dsherman Exp $
 *
 * This program demonstrates the use of the AuctionInc Shipping XS API.
 * An AuctionInc API account is required.
 *
 */

   //
   // Please enter your Account Id that you receive when you registered at AuctionInc site
   //
   $API_AccountId = ''; 
   
    
   //
   // Change the path to directory where you have installed the ShipRateAPI files
   // 
   $shipAPIClass = 'ShipRateAPI.inc';
   
   
   if (! file_exists($shipAPIClass)) die("Unable to locate ShipAPI class file [$shipAPIClass]");
   include($shipAPIClass);

   // Instantiate the Shipping Rate API object
   $shipAPI = new ShipRateAPI($API_AccountId);

   // SSL currently not supported
   $shipAPI->setSecureComm(false);
   
   // curl option (use only if you have the libcurl package installed)
   $shipAPI->useCurl(false);
   
   // Show table of any errors for inspection for this example script
   $showErrors = true;
   
   //************************************************************
   // Set the Detail Level (1, 2 or 3) (Default = 1)
   // DL 1:  minimum required data returned 
   // DL 2:  shipping rate components included
   // DL 3:  package-level detail included
   //************************************************************
   $detailLevel = 3;
   $shipAPI->setDetailLevel($detailLevel);
   
   //************************************************************
   // Set Currency
   // Determines the currency of the returned rates
   // as well as the expected currency of any monetary values set in your call
   // (declared value, item handling fee, fixed fees)
   //************************************************************
   $shipAPI->setCurrency("USD");
   
   
   //************************************************************
   // Set Header Reference Code (optional)
   // can be used to identify and track a subset of calls, 
   // such as a particular seller
   // (trackable in AuctionInc acct => API Statistics)
   //************************************************************
   $sellerID = "123";
   if (isset($sellerID)) $shipAPI->setHeaderRefCode($sellerID);
   

   //**************************************************************
   // Set Origin Address/es for this Shipment
   // (typically fed in from your seller account configuration)
   // Can be set to one or more addresses
   //**************************************************************
   
   // Origin Address #1
   // assign origin code only if items in shipment have multiple origins
   // your origin code will be then referenced at the item level
   $originCode      = "dropship_ma" ;
   $origCountryCode = 'US';   
   $origPostalCode  = '01610';
   $origState       = 'MA';
   
   $shipAPI->addOriginAddress($origCountryCode, $origPostalCode, $origState, $originCode);
   
   // Origin Address #2
   // assign origin code only if items in shipment have multiple origins
   // your origin code will then be referenced at the item level
   $originCode      = "dropship_ca" ; 
   $origCountryCode = 'US';   
   $origPostalCode  = '90210';
   $origState       = 'CA';
   
   $shipAPI->addOriginAddress($origCountryCode, $origPostalCode, $origState, $originCode);
   
   
   //*************************************************************
   // Set Destination Address for this shipment
   // (typically fed in from your cart)
   //*************************************************************
   $destCountryCode = 'US';
   $destPostalCode  = '90210';
   $destState       = 'CA';
   $residential     = true;  // set to true to include any carrier residential fee for delivery to residential address
   
   $shipAPI->setDestinationAddress($destCountryCode, $destPostalCode, $destState, $residential);
   
   
   //*************************************************************
   // Set Carrier Data to rate for this shipment
   // (see API Appendix for Entry Point options per carrier)
   // (typically fed in from your seller account configuration)
   //*************************************************************
   $carrierList = array(
      'UPS'   => array( 'entrypoint' => 'P'),    // Daily Pickup Entry Point 
      'FEDEX' => array( 'entrypoint' => 'D'),    // DropBox Entry Point
      'USPS'  => array( 'entrypoint' => 'R'),    // Retail Entry Point
      'DHL'   => array( 'entrypoint' => 'R')
      // To optionally quote DHL account rates, use below: (requires DHL acct ID and acct country code (2 characters)
      //'DHL'   => array( 'entrypoint' => 'R', 'accesskey'=> 'Replace-with-your-DHL-Account-Nbr', 'acctcountrycode' => 'Replace-with-DHL-Acct-Ctry-Code')
   );
   
   foreach($carrierList AS $code => $data) {
      $shipAPI->addCarrier($code, $data["entrypoint"], 
      (isset($data["accesskey"]) ? $data["accesskey"]: false), 
      (isset($data["postalcode"]) ? $data["postalcode"] : false), 
      (isset($data["acctcountrycode"]) ? $data["acctcountrycode"] : false));
   }

   
   //*************************************************************
   // Set Carrier Services to rate for this shipment
   // (see API Appendix for Carrier Service codes)
   // (on-demand flag is optional, see documentation)
   // (typically fed in from your seller account configuration)
   //*************************************************************  
   $serviceList = array(
      'UPSGND' => array( 'carrier' => 'UPS'),
      'UPS3DS' => array( 'carrier' => 'UPS'),
      'UPS2DA' => array( 'carrier' => 'UPS'),
      'UPSNDS' => array( 'carrier' => 'UPS'),
      'UPSNDA' => array( 'carrier' => 'UPS'),
      'FDXGND' => array( 'carrier' => 'FEDEX'),
      'FDX2D'  => array( 'carrier' => 'FEDEX'),
      'FDXES'  => array( 'carrier' => 'FEDEX'),
      'FDXFO'  => array( 'carrier' => 'FEDEX'),
      'USPPP'  => array( 'carrier' => 'USPS'),
      'USPPM'  => array( 'carrier' => 'USPS'),
      'USPMM'  => array( 'carrier' => 'USPS', 'ondemand'=> true),
      'USPEXP' => array( 'carrier' => 'USPS', 'ondemand'=> true),
      'DHLWPE' => array( 'carrier' => 'DHL'),
      'DHLDE' => array( 'carrier' => 'DHL'),
      'DHLES' => array( 'carrier' => 'DHL')
   );
   
   
   foreach($serviceList AS $scode => $data) {
      $onDemand = isset($data['ondemand']) ? $data['ondemand'] : false;
      // Add service to the API 
      $shipAPI->addService($data['carrier'], $scode, $onDemand);
   } 
   
   
   //********************************************************************
   // Set Maximum Package Weight per service for package splits: OPTIONAL
   // (Default is maximum service weight as established by carrier)
   // This setting will not exclude items which weigh more; it governs
   // when to split to the next package.
   // Settings higher than service maximum are ignored.
   // (typically fed in from your seller account configuration)
   //********************************************************************
   $maxServiceWeights = array(
      'UPSGND' => 100,
      'UPS3DS' => 100,
      'UPS2DA' => 100,
      'UPSNDS' => 100,
      'UPSNDA' => 100,
      'FDXGND' => 100,
      'FDX2D'  => 100,
      'FDXES'  => 100,
      'FDXFO'  => 100,
      'USPPP'  => 50,
      'USPPM'  => 50,
      'USPMM'  => 50,
      'USPEXP' => 50,
      'DHLWPE' => 150
      
   );
   foreach($maxServiceWeights AS $code => $weight) {
      $shipAPI->setMaxServiceWeight($code, $weight);
   }
   
   //**********************************************************************
   // Set Maximum Package Dimensions per service for package splits: OPTIONAL
   // (Default is dervived maximum service volume as established by carrier)
   // This setting will not exclude items which exceed values; it governs
   // when to split to the next package
   // (settings higher than service maximum are ignored)
   // (typically fed in from your seller account configuration)
   //**********************************************************************
   // Length-Width-Height
   $maxServiceDimensions = array(
      'UPSGND' => "20-15-12",
      'UPS3DS' => "20-15-12",
      'UPS2DA' => "20-15-12",
      'UPSNDS' => "20-15-12",
      'UPSNDA' => "20-15-12",
      'FDXGND' => "20-15-12",
      'FDX2D'  => "20-15-12",
      'FDXES'  => "20-15-12",
      'FDXFO'  => "20-15-12",
      'USPPP'  => "12-15-12",
      'USPPM'  => "12-15-12",
      'USPMM'  => "12-15-12",
      'USPEXP' => "12-15-12",
   );
   foreach($maxServiceDimensions AS $code => $dims) {
      $dim = explode("-", $dims);
      $shipAPI->setMaxServiceLength($code, trim($dim[0]));
      $shipAPI->setMaxServiceWidth($code, trim($dim[1]));
      $shipAPI->setMaxServiceHeight($code, trim($dim[2]));
   }
   
   //************************************************************
   // Flag Carrier for Flat Rate Box Qualification: OPTIONAL
   // Item Dimensions are required
   // AuctionInc will price service at the better of the
   // flat (if item/s fit) or calcluated rate
   // USPS (Priority Mail (boxes, envelopes), Express Mail (envelopes)
   //    -- valid values: PKG / ENV / ALL
   // FEDEX (International Priority (boxes)
   //    -- valid values: PKG
   // (typically fed in from your seller account configuration)
   //************************************************************
   $shipAPI->setFlatRatePackaging("USPS", 'PKG');
   
   
   //************************************************************
   // Example Items
    // (typically fed in from cart)
   //************************************************************
    
   //************************************************************
   // Create an array of items to rate
   //************************************************************
   $items = array();  
   
   //************************************************************
   // Test Item 1 (calculated shipping example)
   //************************************************************
   $item = array();
   $item["CalcMethod"] = "C";
   $item["refCode"] = "test_item_1";
   $item["quantity"] = 1;
   $item["lotSize"] = 1;
   $item["packMethod"] = 'T'; // Packageable: T)ogether or S)eparate
   
   // If you have defined multiple origin addresses, all items in shipment
   // must be linked to one of your defined origin codes
   $item["originCode"] = "dropship_ma"; 
   
   // For items packageable together, weight and dims should be for the item
   // For items packageable seperately, weight and dims should be for the box
   $item["weight"] = 9.50; 
   $item["weightUOM"] = "LBS"; // unit of measure
   
   // Dimensions are optional   
   $item["length"] = 10;
   $item["width"] =  8;
   $item["height"] = 4;
   $item["dimUOM"] = "IN";    // unit of measure
   
   // set to item value to include in carrier insurance calculation 
   $item["value"] = 95.00;   
   
   // On Demand Service Qualification (optional)
   // This particular item qualifies for below on-demand services 
   // (used in conjunction with setting the carrier service to on-demand)
   $item["odServices"] = "USPMM, USPEXP";    
   
   // Add this item to Item Array
   $items[] = $item;
   
   
   //************************************************************
   // Test Item 2 (calculated shipping example)
   // (Includes examples of some additional optional settings)
   ///************************************************************
   $item = array();
   $item["refCode"] = "test_item_2";
   $item["CalcMethod"] = "C";
   
   // This alternate Calculation Method will use calculated rates for 
   // international destinations, and free shipping for domestic destinations
   // $item["CalcMethod"] = "CI";  
   
   $item["quantity"] = 2;
   $item["lotSize"] = 1;
   $item["packMethod"] = 'T'; // Packageable: T)ogether or S)eparate
   
   // If you have defined multiple origin addresses, all items in shipment
   // must be linked to one of your defined origin codes
   $item["originCode"] = "dropship_ca"; 
   
   // For items packageable together, weight and dims should be for the item
   // For items packageable seperately, weight and dims should be for the box
   $item["weight"] = 20.25; 
   $item["weightUOM"] = "LBS";
   
   // Dimensions are optional   
   $item["length"] = 2;
   $item["width"] =  3;
   $item["height"] = 1;
   $item["dimUOM"] = "IN"; 
   
   // set to item value to include in carrier insurance calculation 
   $item["value"] = 14.95;   
   
   // On Demand Service Qualification: OPTIONAL
   // This particular item qualifies for below on-demand service 
   /// (used in conjunction with setting the carrier service to on-demand)
   $item["odServices"] = "USPMM";  
   
   // Item-Level Handling: OPTIONAL
   // Added for each quantity of this item
   //$item["handling"] = .75;
   
   // Special Accessorial Carrier Service Fees: OPTIONAL
   // This particular item needs special carrier service accessorial fees to be added 
   //$item["specCarrierSvcs"] = "AddlHandling, SignatureReqUPS";
  
   // Uncomment to add this item to this shipment
   // $items[] = $item;
    
   
   //************************************************************
   // Test Item 3 (fixed fee shipping example)
   //************************************************************
   $item = array();
   $item["CalcMethod"] = "F";
   $item["refCode"] = "test_item_3";
   
   // If you have defined multiple origin addresses, all items in shipment
   // must be linked to one of your defined origin codes (including fixed fee items)
   $item["originCode"] = "dropship_ca"; 
   
   $item["FeeType"] = "F"; // F)ixed Dollar Amt
   $item["quantity"] = 2;
   
   // When multiple fixed rate items in cart, the highest fixedAmt_11 is applied first, 
   // then the appropriate fixedAmt_2 for each of the other items
   $item["fixedAmt_1"] = 3.00;  // ship rate for first quantity
   $item["fixedAmt_2"] = 2.00;  // ship rate for subsequent quantities

   // Uncomment to add this item to this shipment
   // $items[] = $item;
   
    
   //************************************************************
   // Test Item 4 (free shipping example)
   //************************************************************
   $item = array();
   $item["CalcMethod"] = "N"; // No Charge
   $item["refCode"] = "test_item_3";
   $item["quantity"] = 1;
   // Uncomment to add this item to this shipment
   // $items[] = $item;
   
   
   //************************************************************
   // End Example Items
   //************************************************************

   
   
   //************************************************************
   // Add Item Data from Item Array to API Object
   //************************************************************
   foreach ($items AS $val){
      
      $calcCodes = array('C', 'CI');   
      
      if (in_array($val["CalcMethod"], $calcCodes)){
         
         $shipAPI->addItemCalc($val["refCode"], $val["quantity"], $val["weight"], $val['weightUOM'], $val["length"], $val["width"], $val["height"], $val["dimUOM"],  $val["value"], $val["packMethod"], $val["lotSize"], $val["CalcMethod"]);
         
         if (isset($val["originCode"]))          $shipAPI->addItemOriginCode($val["originCode"]);
         if (isset($val["odServices"]))          $shipAPI->addItemOnDemandServices($val["odServices"]);
         if (isset($val["specCarrierSvcs"]))     $shipAPI->addItemSpecialCarrierServices($val["specCarrierSvcs"]);
         if (isset($val["handling"]))            $shipAPI->addItemHandlingFee($val["handling"]);
   
      } elseif ($val["CalcMethod"] == "F"){
         $shipAPI->addItemFixed($val["refCode"], $val["quantity"], $val["FeeType"], $val["fixedAmt_1"], $val["fixedAmt_2"], $val["fixedFeeCode"]);   
         if (isset($val["originCode"])) $shipAPI->addItemOriginCode($val["originCode"]); 
         
      } elseif ($val["CalcMethod"] == "N"){
         $shipAPI->addItemFree($val["refCode"], $val["quantity"]);    
      }
   }
 
   
   //************************************************************
   // Call the AuctionInc XS API
   // Utilizes xml data for all rating information 
   // and supports infinite sellers 
   //************************************************************
    
   echo '<h2>GetItemShipRatesXS Method</h2>';
   echo 'This method determines shipping rates using shipping preferences that are passed in XML<p>';
   
   $ok = $shipAPI->GetItemShipRateXS( $shipRates );
   if ($ok) {
      // this would typically feed the results back to your cart
      displayRates($shipRates, $detailLevel);
   } else {
      echo 'Sorry, but we were unable to determine shipping rates.';
      
      if ($showErrors == true){
         $str =  "<P><table border=1><tr><th>Code</th><th>Message</th><th>Severity</th>";
         foreach ($shipRates["ErrorList"] AS $k => $v){
            $code     = $v["Code"];      
            $message  = $v["Message"];      
            $severity = $v["Severity"];  
            $str .= "<tr><td>$code</td><td>$message</td><td>$severity</td></tr>";    
         } 
         $str .=  "</table>";
         echo $str;
      } 
   }
   
   
   /**
   * Displays shipping rates in an inspection table and example dropdown
   * @param    assocArray     Array of results from the Shipping Rates API
   * @return   void
   **/
   function displayRates($shipRates, $detailLevel) {
      
      $str = "";  
      $header =  "<table border=1><tr BGCOLOR=\"#99CCFF\"><th>Valid</th><th>CarrierCode</th><th>ServiceCode</th><th>ServiceName</th><th>CalcMethod</th><th>Total Rate</th>";
      if ($detailLevel == 2 || $detailLevel == 3) 
        $header .= "<th>Carrier Rate</th><th>Surcharges</th><th>Fuel Surcharge</th><th>Handling</th><th>Declared Value</th><th>Insurance</th><th>Pkg Count</th><th>Weight</th>";
      
         for($i=0, $c=sizeof($shipRates['ShipRate']); $i < $c; $i++) {
            
            $valid = $shipRates['ShipRate'][$i]['Valid'];
            $carrierCode = $shipRates['ShipRate'][$i]['CarrierCode'];
            $serviceCode = $shipRates['ShipRate'][$i]['ServiceCode'];
            $serviceName = $shipRates['ShipRate'][$i]['ServiceName'];
            $calcMethod  = $shipRates['ShipRate'][$i]['CalcMethod'];
            $rate = $shipRates['ShipRate'][$i]['Rate']; 
            
            if ($detailLevel == 2 || $detailLevel == 3){
               $carrierRate      =  $shipRates['ShipRate'][$i]['CarrierRate'];   
               $fuelSurcharge    =  $shipRates['ShipRate'][$i]['FuelSurcharges'];   
               $surcharges       =  $shipRates['ShipRate'][$i]['Surcharges'];   
               $handlingFees     =  $shipRates['ShipRate'][$i]['HandlingFees'];  
               $declaredValue    =  $shipRates['ShipRate'][$i]['DeclaredValue'];  
               $InsuranceCharges =  $shipRates['ShipRate'][$i]['InsuranceCharges'];   
               $packageCount     =  $shipRates['ShipRate'][$i]['PackageCount'];   
               $ttlWeight        =  $shipRates['ShipRate'][$i]['Weight'];   
               $pkgDetail        =  isset($shipRates['ShipRate'][$i]['PackageDetail']) ? $shipRates['ShipRate'][$i]['PackageDetail'] : "";
            }
            
            $str  .=  "<tr BGCOLOR=\"#DEDEDE\"><td>$valid</td><td>$carrierCode</td><td>$serviceCode</td><td>$serviceName</td><td align=middle>$calcMethod</td><td align=right>$rate</td>";
            if ($detailLevel == 2 || $detailLevel == 3){ 
              $str .= "<td align=right>$carrierRate</td><td align=right>$surcharges</td><td align=right>$fuelSurcharge</td><td align=right>$handlingFees</td>";
              $str .= "<td align=right>$declaredValue</td><td align=right>$InsuranceCharges</td><td align=middle>$packageCount</td><td align=right>$ttlWeight</td>";
            }
            
            if ($detailLevel == 3){
               // package detail
               $pcnt=1;
               if (is_array($pkgDetail)){
                  foreach ($pkgDetail AS $k => $v){
                     $pshipRate    = $v["ShipRate"];
                     $pcarRate     = $v["CarrierRate"];
                     $pSurcharge   = $v["Surcharge"];
                     $pFuelSurcharge  = $v["FuelSurcharge"];
                     $pHandling    = $v["Handling"];
                     $pDecValue    = $v["DeclaredValue"];
                     $pInsurance   = $v["Insurance"];
                     $pWeight      = $v["Weight"];
                     $pLength      = $v["Length"]; // not displayed
                     $pWidth       = $v["Width"];  // not displayed
                     $pHeight      = $v["Height"]; // not displayed
                     $pOrigin      = $v["Origin"];
                     $pOversizeCode= strtolower($v["OversizeCode"]);
                     $pFlatRateCode= isset( $v["FlatRateCode"]) ? $v["FlatRateCode"] : "";
                     $pCode        = (isset($pFlatRateCode) && $pFlatRateCode != "NONE") ? $pFlatRateCode : $pOversizeCode;
                     $pPackMethod  = $v["PackMethod"];
                     $pQuantity    = $v["Quantity"];
                     $pkgItem      = $v["PkgItem"];  
                     
                     

                     $str .= "<tr BGCOLOR=\"#FFFF99\"><td colspan=5><b>Package $pcnt Detail</b> (PackMethod: $pPackMethod; ShipOrigin: $pOrigin; Code: $pCode) </td>";
                     $str .= "<td align=right>$pshipRate</td><td align=right>$pcarRate</td><td align=right>$pSurcharge</td><td align=right>$pFuelSurcharge</td>";
                     $str .= "<td align=right>$pHandling</td><td align=right>$pDecValue</td> <td align=right>$pInsurance</td><td align=middle>$pQuantity</td><td align=middle>$pWeight</td>";
                     $str .= "</tr>";
                     
                     if (is_array($pkgItem)){
                        $icnt = 1;
                        $str .= "<tr BGCOLOR=\"#FFFFCC\"><TH colspan=11 align=right>Item Detail</TH><TH>Ref Code</TH><TH>Quantity</TH><TH>Weight</TH></tr>";
                        foreach ($pkgItem AS $k1 => $v1){
                          $pItemRefCode = $v1["RefCode"];
                          $pItemQty     = $v1["Qty"];
                          $pItemWeight  = $v1["Weight"];   
                          
                          $str .= "<tr BGCOLOR=\"#FFFFCC\"><td colspan=11 align=right>item $icnt </td><td> $pItemRefCode</td><td align=middle>$pItemQty</td><td align=right>$pItemWeight</td></tr>";
                          $icnt++;
                        }
                     }
                     $pcnt++;
                  }
               }
            
               if ($i < $c-1){           
                  $str .=  "<tr BGCOLOR=\"#99CCFF\"><th>Valid</th><th>CarrierCode</th><th>ServiceCode</th><th>ServiceName</th><th>CalcMethod</th><th>Total Rate</th>";
                  $str .= "<th>Carrier Rate</th><th>Surcharges</th><th>Fuel Surcharge</th><th>Handling</th><th>Declared Value</th><th>Insurance</th><th>Pkg Count</th><th>Weight</th>";
               }
      
            }
         }
         
         echo $header . $str;
         echo "</table>";
         
         echo "<p>Example FORM SELECT Input:<br><form>" . genSelectHTML($shipRates) . '</form>';
    }
    
        
   /**
   * Displays shipping rates in a dropdown
   * @param    assocArray     Array of results from the Shipping Rates API
   * @param    string         name for the SELECT form element (default 'shiprate')
   * @param    string         format of the VALUE for the SELECT that is returned in the POST (see Format below)
   * @param    string         format of the display for each SELECT option (see Format below)
   * @param    int            number of rows that the SELECT will display automatically (default 1)
   * @param    string         class name to use in the SELECT if you are using CSS default (none)
   * @param    string         option value when there are no rates (default '0')
   * @param    string         message to display if no rates are available (default 'Unable to determine')
   * @return   void
   *
   * NOTE:  Formating of the SELECT values and display are controlled by supplying strings with tags that will
   *        be replaced by the function with the appropriate text.  The tags are as follows:
   *          Tag - Value
   *           R - the shipping rate unformatted
   *           F - the shipping rate formatted (i.e. $12.95) 'Free' is displayed when rate is zero (0)
   *           S - the service code
   *           C - carrier code
   *           N - service name (i.e. 'UPS Ground')
   *           
   *        An example format string 'N - F' will be displayed like 'UPS Ground - $12.50'
   **/
   function genSelectHTML($shipRates, $fieldName='shiprate', $valueFormat='C,R', $displayFormat='N - F', $size=4, $class='', $noRatesVal='0', $noRatesMsg='Unable to determine') {
         
      $html = "<SELECT NAME=\"$fieldName\"" . (! empty($class) ? " CLASS=\"$class\"" : '') . " SIZE=\"$size\">\n";
      $c=sizeof($shipRates['ShipRate']);

      $n=0;    // the number of valid rates
      for($i=0; $i < $c; ++$i) {
         $valid = $shipRates['ShipRate'][$i]['Valid'];
         if (strcmp($valid, 'true') !== 0) continue;
         ++$n;
         
         $vals['R'] = $shipRates['ShipRate'][$i]['Rate'];
         $vals['F'] = $shipRates['ShipRate'][$i]['Rate'] > 0 ? ('$' . number_format($shipRates['ShipRate'][$i]['Rate'],2) ) : 'Free';
         $vals['S'] = $shipRates['ShipRate'][$i]['ServiceCode'];
         $vals['C'] = $shipRates['ShipRate'][$i]['CarrierCode'];
         $vals['N'] = $shipRates['ShipRate'][$i]['ServiceName'];

         // Iterate over the format strings and substitute the tags with the appropriate values
         $value = '';
         for($s=0, $l=strlen($valueFormat); $s < $l; ++$s) {
            $char = $valueFormat{$s};
            $value .= urlencode( isset($vals[$char]) ? $vals[$char] : $char );
         }

         $display = '';
         for($s=0, $l=strlen($displayFormat); $s < $l; ++$s) {
            $char = $displayFormat{$s};
            $display .= isset($vals[$char]) ? $vals[$char] : $char;
         }
         
         $selected = $n==1 ? ' SELECTED' : '';
         $html .= "<OPTION VALUE=\"$value\"$selected>$display\n";
      }
      
      if ($n == 0) {
         $html .= "<OPTION VALUE=\"$noRatesVal\">$noRatesMsg\n";
      }
      $html .=  '</SELECT>';
      
      return $html;
   }
    
?>