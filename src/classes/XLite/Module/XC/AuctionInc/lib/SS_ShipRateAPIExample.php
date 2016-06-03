<?php
/*
 *
 * Copyright (c) Paid, Inc.  All rights reserved.
 *
 * $Id: SS_ShipRateAPIExample.php,v 1.6 2015/03/08 17:34:56 dsherman Exp $
 *
 * This program demonstrates the use of the AuctionInc Shipping SS API.
 * An SS API account, with account information configuration in AuctionInc, is required.
 *
 */

   //
   // Please enter your account Id that you receive when you register at AuctionInc site
   //
   $API_AccountId = ''; 
   
   
   //
   // Change the path to directory where you have installed the ShipRate API files
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
   
   // Show table of any errors for inspection for this example script
   $showErrors = true;
   
   //************************************************************
   // Set Destination Address for this API call
   // (These values would typically come from your cart)
   //************************************************************
   $destCountryCode = 'US';
   $destPostalCode  = '90210';
   $residential     = true;
   $shipAPI->setDestinationAddress($destCountryCode, $destPostalCode, '', $residential);

     
   //************************************************************
   // Example Items
   // (Item data would typically be fed in from your cart)
   //************************************************************ 
   
   // Create an array of items to rate
   $items = array();  
   
   //************************************************************
   // Test Item 1 (calculated shipping example)
   //************************************************************
   $item = array();
   $item["CalcMethod"] = "C";
   $item["refCode"] = "test_item_1";
   $item["quantity"] = 1;
   $item["lotSize"] = 1;
   $item["packMethod"] = 'T'; // Packageable T)ogether or S)eparate
   
   // For items packageable together, weight and dims should be for the item
   // For items packageable seperately, weight and dims should be for the box
   $item["weight"] = 11.50; 
   $item["weightUOM"] = "LBS"; // unit of measure
   
   // Dimensions are optional   
   $item["length"] = 1;
   $item["width"] =  1;
   $item["height"] = 1;
   $item["dimUOM"] = "IN";    // unit of measure
   $item["value"] = 95.00;   // insurable item value
   
   // On Demand Service Qualification (optional)
   // This particular item qualifies for below on-demand services 
   // (Used only if you have set these service/s to on-demand in your AuctionInc account)
   $item["odServices"] = "USPMM, USPEXP";    
   
   // Add this item to Item Array
   $items[] = $item;
   
   
   //**************************************************************
   // Test Item 2 (calculated shipping example)
   // This item includes examples of a variety of optional features
   //**************************************************************
   $item = array();
   $item["refCode"] = "test_item_2";
   $item["CalcMethod"] = "C";
   
   // This alternate Calculation Method will use calculated rates for 
   // international destinations, and free shipping for domestic destinations
   // $item["CalcMethod"] = "CI";  
   
   $item["quantity"] = 1;
   $item["lotSize"] = 1;
   $item["packMethod"] = 'T'; // Packageable T)ogether or S)eparate
   
   // For items packageable together, weight and dims should be for the item
   // For items packageable seperately, weight and dims should be for the box
   $item["weight"] = 20.5; 
   $item["weightUOM"] = "LBS";
   
   // Dimensions are optional   
   $item["length"] = 2;
   $item["width"] =  3;
   $item["height"] = 1;
   $item["dimUOM"] = "IN"; 
   $item["value"] = 14.95;   // insurable item value 
   
   // Item Origin Code (optional)
   // This item is shipped from an origin different than the account default origin
   // Origin Code is defined in your AuctionInc account
   // This is an example if you had defined an origin code = "CA"; 
   // $item["originCode"] = "CA"; 
   
   // Item Supplemental Handling Code (optional)
   // This item needs item-specific handling charge (in addition to package/order level handling configured in your account)
   // The supplemental handling code is defined in your AuctionInc account
   // Handling codes allow linkage to specified carriers/services and to quantity sets
   // This is an example if you had defined a supplemental handling code = "fragile"
   //$item["suppHandlingCode"] = "fragile";
   
   // Item Supplemental Handling Fee (optional)
   // Add specified dollar amount to shipping rate 
   // Added for each quantity of this item
   //$item["suppHandlingFee"] = 1.50;
   
   // On Demand Service Qualification (optional)
   // This particular item qualifies for below on-demand service 
   // (Used only if you have set this service/s to on-demand in your AuctionInc account)
   $item["odServices"] = "USPMM";  
   
   // Special Accessorial Carrier Service Fees (optional)
   // This particular item needs special carrier service accessorial fees to be added 
   //$item["specCarrierSvcs"] = "AddlHandling, SignatureReqUPS";
  
   // Uncomment to add this item to this shipment
   // $items[] = $item;
    
   
   //************************************************************
   // Test Item 3 (fixed rate shipping)
   //************************************************************
   $item = array();
   $item["CalcMethod"] = "F";
   $item["refCode"] = "test_item_3";
   $item["FeeType"] = "F"; // F)ixed Dollar Amount or C)ode 
   $item["quantity"] = 2;
   // When multiple fixed rate items in cart, the highest fixedAmt1 is applied first, then the appropriate
   // fixedAmt2 for each of the other items
   $item["fixedAmt_1"] = 3.00;  // ship rate for first quantity
   $item["fixedAmt_2"] = 2.00;  // ship rate for subsequent quantities
   $item["fixedFeeCode"] = "";  // Fixed Fee Code is defined in your AuctionInc account 

   // Uncomment to add this item to this shipment
   // $items[] = $item;
   
   
   //************************************************************
   // Test Item 4 (free shipping)
   //************************************************************
   $item = array();
   $item["CalcMethod"] = "N"; // No Charge
   $item["refCode"] = "test_item_3";
   $item["quantity"] = 1;
   // Uncomment to add this item to this shipment
   // $items[] = $item;
   
   
   //************************************************
   // End Example Items
   //************************************************ 
   
   
   
   //************************************************
   // Add Item Data from Item Array to API Object
   //************************************************
   foreach ($items AS $val){
         
      $calcCodes = array('C', 'CI');   
      
      if (in_array($val["CalcMethod"], $calcCodes)){
        
         $shipAPI->addItemCalc($val["refCode"], $val["quantity"], $val["weight"], $val['weightUOM'], $val["length"], $val["width"], $val["height"], $val["dimUOM"],  $val["value"], $val["packMethod"], $val["lotSize"], $val["CalcMethod"]);
           
         if (isset($val["originCode"]))          $shipAPI->addItemOriginCode($val["originCode"]);
         if (isset($val["odServices"]))          $shipAPI->addItemOnDemandServices($val["odServices"]);
         if (isset($val["suppHandlingCode"]))    $shipAPI->addItemSuppHandlingCode($val["suppHandlingCode"]);
         if (isset($val["suppHandlingFee"]))     $shipAPI->addItemHandlingFee($val["suppHandlingFee"]);
         if (isset($val["specCarrierSvcs"]))     $shipAPI->addItemSpecialCarrierServices($val["specCarrierSvcs"]);
   
      } elseif ($val["CalcMethod"] == "F"){
         $shipAPI->addItemFixed($val["refCode"], $val["quantity"], $val["FeeType"], $val["fixedAmt_1"], $val["fixedAmt_2"], $val["fixedFeeCode"]);    

      } elseif ($val["CalcMethod"] == "N"){
         $shipAPI->addItemFree($val["refCode"], $val["quantity"]);    
      }
   }
 
   
   //************************************************
   // Call the AuctionInc SS API
   // Utilizes account configured info for default 
   // origin address and enabled carrier services 
   //************************************************
    
   echo '<h2>GetItemShipRatesSS Method</h2>';
   echo 'This method utilizes shipping preferences that are pre-configured at the AuctionInc website.<p>';
   
   $ok = $shipAPI->GetItemShipRateSS( $shipRates );
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
                     
                     

                     $str .= "<tr BGCOLOR=\"#FFFFCC\"><td colspan=5><b>Package $pcnt Detail</b> (PackMethod: $pPackMethod; ShipOrigin: $pOrigin; Code: $pCode) </td>";
                     $str .= "<td align=right>$pshipRate</td><td align=right>$pcarRate</td><td align=right>$pSurcharge</td><td align=right>$pFuelSurcharge</td>";
                     $str .= "<td align=right>$pHandling</td><td align=right>$pDecValue</td> <td align=right>$pInsurance</td><td align=middle>$pQuantity</td><td align=middle>$pWeight</td>";
                     $str .= "</tr>";
                     
                     if (is_array($pkgItem)){
                        $icnt = 1;
                        $str .= "<tr BGCOLOR=\"#FFFF99\"><TH colspan=11 align=right>Item Detail</TH><TH>Ref Code</TH><TH>Quantity</TH><TH>Weight</TH></tr>";
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