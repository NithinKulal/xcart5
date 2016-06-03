<?php
/*
 *
 * Copyright (c) Paid, Inc.  All rights reserved.
 *
 * $Id: ShipRateParserXML.inc,v 1.5 2005/05/25 12:54:36 jmartin Exp $
 *
 * This program is part of the Paid Shipping Rate API toolkit for PHP and is responsible for parsing the XML
 * that is returned by the rate engine.
 *
 */

/**
 * SAX Parser
 *
 * @author "Victor Didovicher" <victord@paid.com>
 * @date 2005.05.17
 * @version 1.0
 * @copyright (c) Paid, Inc.  All rights reserved.
 */
class ShipRateParserXML
{
   var $tag;
   var $parser;
   var $dom;
   var $_current_tag;
   var $_err_state;
   var $_error_cnt;

   /**
    * Default Constructor
    * @access public
    */
   function __construct() {
      $this->_init();
   }

   /**
    * Initialize the parser object
    * @return bool 
    * @access private
    */
   function _init()
   {
      $this->_current_tag = false;
      $this->_err_state   = false;
      $this->_error_cnt   = 0;
      $this->dom          = array();

      if (isset($this->parser) && is_resource($this->parser)) {
         @xml_parser_free($this->parser);
      }
      $this->parser = @xml_parser_create();
      if (is_resource($this->parser)) {
         xml_set_object($this->parser, $this);
         xml_set_element_handler($this->parser, 'startHandler', 'endHandler');
         xml_set_character_data_handler($this->parser, 'cdataHandler');
         xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
         return true;
      }
      return false;
   }

   /**
    * XML Element Handler (start)
    * @param Object xml parser
    * @param String Tag
    * @param Array Attribute Array
    * @return none
    * @access public
    */
   function parse($string)
   {
      if (isset($this->parser) && is_resource($this->parser)) {
         xml_parse($this->parser, $string, true);
         xml_parser_free($this->parser);
         $this->parser = null;
         return $this->dom;
      }
      return false;
   }

   ### ----------------------------------------------------------------------
   ### Methods to Override - OVERRIDE ONLY METHODS -
   ### ----------------------------------------------------------------------

   /**
    * XML Element Handler (start)
    * @param Object xml parser
    * @param String Tag
    * @param Array Attribute Array
    * @return none
    * @access public
    */
   function startHandler($parser, $tag, $attribs)
   {
      $this->_current_tag = $tag;
      $attribs = array_change_key_case($attribs,CASE_LOWER);
      switch ($tag) {
         case 'ErrorList': xml_set_character_data_handler($this->parser,'_cdataHandler_Error');
                           $this->dom = array();
                           $this->dom['ErrorList'] = array();
                           $this->_err_state = true;
                           break;
         case 'Error':     $this->dom['ErrorList'][$this->_error_cnt] = array();
                           break;
      }
   }

   /**
    * XML Element Handler (end)
    * @param object $parser xml parser
    * @param string $tag string that will be provided by the parser
    * @return none
    * @access public
    */
   function endHandler($parser, $tag)
   {
      $this->_current_tag = null; // <<<<< WATCH THIS, cdataHandlers called 3 times: tag space and \n
      switch ($tag) {
         case 'ErrorList': break;
         case 'Error':     $this->_error_cnt++; break;
      }  
   }

   ### ----------------------------------------------------------------------
   ### Default Character Data Handler :: Handle ErrorList
   ### ----------------------------------------------------------------------

   /**
    * XML Character Data Handler
    * @param object $parser xml parser
    * @param string $data string that will be provided by the parser
    * @return none
    * @access public
    */
   function cdataHandler($parser, $data)
   { }

   /**
    * XML Character Data Handler
    * @param object $parser xml parser
    * @param string $data string that will be provided by the parser
    * @return none
    * @access public
    */
   function _cdataHandler_Error($parser, $val)
   {
      static $map;
      if (!$this->_err_state || ($this->_current_tag == null) || empty($this->_current_tag)) return;
      if (!isset($map)) {
         $map = array(
            'code'     => 'Code',
            'message'  => 'Message',
            'severity' => 'Severity');
      }
      $this->cdataAdapter($val, $map, $this->_error_cnt, $this->dom['ErrorList']);
   }

   ### ----------------------------------------------------------------------
   ### Support Methods
   ### ----------------------------------------------------------------------

   /**
    * Used by cdataHandler methods to store the data into the object container in the proper position
    * @param    string      text just parsed from XML
    * @param    assocArray  Element list with names mapped
    * @param    string      Node name that current tag will be place in the container, blank if
    * @param    assocArray(ref)   reference point of $this->_res where the node will reside
    * @return   void
    */
   function cdataAdapter($data, $map, $nodeName, & $container)
   {
      $str = strtolower($this->_current_tag);
      if (isset($map[$str])) {

         // Get a reference into the container where data should be placed
         if ( is_null($nodeName) ) {
            $ref = & $container;
         } else {
            // Double check to see if the node has been created yet
            if (! isset($container[$nodeName])) $container[$nodeName] = array();
            $ref = & $container[$nodeName];
         }

         // Get the element name from the map
         $key = $map[$str];

         if (isset($ref[$key]))
            $ref[$key] .= $data;
         else
            $ref[$key] = $data;
      }
   }
}
?>