<?php
/*
 *
 * Copyright (c) Paid, Inc.  All rights reserved.
 *
 * $Id: ShipRateParserXMLItem.inc,v 1.11 2015/03/25 15:34:19 dsherman Exp $
 *
 * This program is part of the Paid Shipping Rate API toolkit for PHP and is responsible for parsing certain XML
 * elements that are returned by the rate engine.
 *
 */

class ShipRateParserXMLItem extends ShipRateParserXML
{
   var $_rate_cnt = 0;
   var $_pkg_cnt = 0;
   var $_pkgItem_cnt = 0;
   var $debug = false;


   ### --------------------------------------------------------------------
   ### XML Parser Functions
   ### --------------------------------------------------------------------

   /**
    * Handle start element
    *
    * @access private
    * @param  resource  xml parser resource
    * @param  string    name of the element
    * @param  array     attributes
    */
   function startHandler($xp, $name, $attribs)
   {
      $this->_current_tag = $name;
      $attribs = array_change_key_case($attribs,CASE_LOWER);

      if ($this->debug) echo " + startHandler() : $name<br>";
      
      if ( $name == "ShipRate" 
        && isset($this->dom['ShipRate'][$this->_rate_cnt]["PackageDetail"]) 
        && is_array($this->dom['ShipRate'][$this->_rate_cnt]["PackageDetail"][$this->_pkg_cnt])){
         parent::startHandler($xp, $name, $attribs);  
      } else {   
         
         switch ($name) {
            case 'ItemShipRate': $this->dom = array();
                                 break;
            case 'ShipRateList': xml_set_character_data_handler($this->parser,'cdataHandler_ShipRate');
                                 $this->dom['ShipRate'] = array();
                                 break;
            case 'ShipRate':     $this->dom['ShipRate'][$this->_rate_cnt] = array();
                                 break;
            case 'PackageDetail':$this->_pkg_cnt = 0;
                                 break;                     
            case 'Package':      xml_set_character_data_handler($this->parser,'cdataHandler_Package');
                                 $this->dom['ShipRate'][$this->_rate_cnt]["PackageDetail"][$this->_pkg_cnt] = array();
                                 $this->_pkgItem_cnt = 0;
                                 break;
            case 'PkgItem':      xml_set_character_data_handler($this->parser,'cdataHandler_PkgItem');
                                 $this->dom['ShipRate'][$this->_rate_cnt]["PackageDetail"][$this->_pkg_cnt]["PkgItem"][$this->_pkgItem_cnt]  = array();
                                 break;                     
            default:             parent::startHandler($xp, $name, $attribs);
         }
      }
   }

   /**
    * Handle end element
    *
    * @access private
    * @param  resource  xml parser resource
    * @param  string    name of the element
    */
   function endHandler($xp, $name)
   {
      $this->_current_tag = null; // <<<<< WATCH THIS, cdataHandlers called 3 times: tag space and \n
      if ($this->debug) echo " + endHandler() : $name<br>";

      if ( $name == "ShipRate" && isset($this->dom['ShipRate'][$this->_rate_cnt]["PackageDetail"][$this->_pkg_cnt])){
         parent::endHandler($xp, $name);
      } else {  
      
         switch ($name) {
            case 'ItemShipRate': xml_set_character_data_handler($this->parser,'cdataHandler');
                                 break;
            case 'ShipRateList': xml_set_character_data_handler($this->parser,'cdataHandler');
                                 break;
            case 'ShipRate':     $this->_rate_cnt++;
                                 break;
            case 'PackageDetail': xml_set_character_data_handler($this->parser,'cdataHandler_ShipRate');
                                 break; 
            case 'Package':      $this->_pkg_cnt++;
                                 break;  
            case 'PkgItem':      $this->_pkgItem_cnt++;
                                 break;                                                   
            default:             parent::endHandler($xp, $name);
         }
      }
   }
   
   //////////////////////////////////////////////////////////////////////////
   // Custom CDATA Handlers
   //////////////////////////////////////////////////////////////////////////

   /**
    * Handle character data within SHIPRATE element
    * 
    * @access private
    * @param  resource  xml parser resource
    * @param  string    value of the resource
    */
   function cdataHandler_ShipRate($xp, $val)
   {
      static $map;
      if ($this->_err_state || empty($this->_current_tag)) return;
      if (!isset($map)) {
         $map = array(
            'valid'       => 'Valid',
            'carriercode' => 'CarrierCode',
            'servicecode' => 'ServiceCode',
            'servicename' => 'ServiceName',
            'calcmethod'  => 'CalcMethod',
            'rate'        => 'Rate',
            'carrierrate'      => 'CarrierRate',
            'fixedrate'        => 'FixedRate',
            'surcharges'       => 'Surcharges',
            'fuelsurcharges'   => 'FuelSurcharges',
            'handlingfees'     => 'HandlingFees',
            'declaredvalue'    => 'DeclaredValue',
            'insurancecharges' => 'InsuranceCharges',
            'weight'           => 'Weight',
            'packagecount'     => 'PackageCount',
            'flatratecode'     => 'FlatRateCode');
            
      }
      $this->cdataAdapter($val, $map, $this->_rate_cnt, $this->dom['ShipRate']);
   }
   
    /**
    * Handle character data within PACKAGE element
    * 
    * @access private
    * @param  resource  xml parser resource
    * @param  string    value of the resource
    */
    
   function cdataHandler_Package($xp, $val)
   {
      static $map;
      if ($this->_err_state || empty($this->_current_tag)) return;
      if (!isset($map)) {
         $map = array(
            'quantity'    => 'Quantity',
            'packmethod'  => 'PackMethod',
            'origin'      => 'Origin',
            'declaredvalue' => 'DeclaredValue',
            'weight'      => 'Weight',
            'length'      => 'Length',
            'width'       => 'Width',
            'height'      => 'Height',
            'oversizecode'  => 'OversizeCode',
            'splitcode'   => 'SplitCode',
            'flatratecode'  => 'FlatRateCode',
            'carrierrate' => 'CarrierRate',
            'fixedrate'   => 'FixedRate',
            'surcharge'   => 'Surcharge',
            'fuelsurcharge' => 'FuelSurcharge',
            'insurance'   => 'Insurance',
            'handling'    => 'Handling',
            'shiprate'    => 'ShipRate');
            
      }
      $this->cdataAdapter($val, $map, $this->_pkg_cnt, $this->dom['ShipRate'][$this->_rate_cnt]["PackageDetail"]);
   }
   
   
   /**
    * Handle character data within PKGITEM element
    * 
    * @access private
    * @param  resource  xml parser resource
    * @param  string    value of the resource
    */
    
   function cdataHandler_PkgItem($xp, $val)
   {
      static $map;
      if ($this->_err_state || empty($this->_current_tag)) return;
      if (!isset($map)) {
         $map = array(
            'refcode'   => 'RefCode',
            'qty'       => 'Qty',
            'weight'    => 'Weight');
            
      }
      $this->cdataAdapter($val, $map, $this->_pkgItem_cnt, $this->dom['ShipRate'][$this->_rate_cnt]["PackageDetail"][$this->_pkg_cnt]["PkgItem"]);
   }

   /**
    * Handle character data within ItemShipRate element
    *
    * @access private
    * @param  resource  xml parser resource
    * @param  string    value of the resource
    */
   function cdataHandler($xp, $val)
   {
      static $map;
      if ($this->_err_state || empty($this->_current_tag)) return;
      $map = array('currency' => 'Currency');
      $this->cdataAdapter($val, $map, null, $this->dom);   
   }
}

?>