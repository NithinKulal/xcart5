<?php
/*
 *
 * Copyright (c) Paid, Inc.  All rights reserved.
 *
 * $Id: ShipRateSocketPost.inc,v 1.15 2014/11/05 23:10:57 dsherman Exp $
 *
 * This program is part of the Paid Shipping Rate API toolkit for PHP and is responsible for the actual communications
 * between the client application and the shipping rate engine web service.  It uses standard PHP file access functions 
 * to be flexible, and curl as an option.
 *
 */

class ShipRateSocketPost
{
   var $useragent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";
   var $debug     = false;
   var $useCURL   = false;
   var $verbose   = false;
   var $timeout   = 20;       // timeout (seconds) for communications

   var $url = 'api.auctioninc.com';
   var $uri = '/websvc/shire';
   var $port = 443;		// SSL (https)

   /**
   * Used to override the communications timeout setting (default 10 seconds)
   * @param    integer     seconds to timeout
   * @return   void
   **/
   function setTimeout($v) { $this->timeout = (int)$v; }
   
   function setVerbose($v) { $this->verbose = is_bool($v) ? $v : false; }
   function setDebug($v)   { $this->debug   = is_bool($v) ? $v : false; }
   function setURL($url)   { $this->url     = $url; }
   function setURI($uri)   { $this->uri     = $uri; }
   function setPort($port) { $this->port    = $port; }
   function setCURL($v)    { $this->useCURL   = is_bool($v) ? $v : false; }

   /**
    * Internal method that actually does the work of getting/posting pages
    * @param   string  POST fields/data for submission
    * @param   array   Array of optional headers
    * @param   array   String of optional cookies (name=value;name=value);
    * @param   &string Return result from host
    * @param   &string Error message if call failed
    * @return  boolean Indicates success
    */
   function post($queryData, $reqHeaders=false, &$respContent, &$respHeaders, &$errorMsg)
   {
      
      if (!is_array($reqHeaders)) $reqHeaders = array();
      
      switch ($this->port) {
         case 443 : 
            $host = "ssl://" . $this->url;
            break;
         default : 
            $host = "" . $this->url;
      }
      
      
      if ($this->useCURL == true){
         
         if (! function_exists('curl_init')) die('PHP Curl module must be configured');
         $ch = curl_init();

         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $queryData);
         curl_setopt($ch, CURLOPT_URL, $host.$this->uri);
         curl_setopt($ch, CURLOPT_HEADER, 1);
         curl_setopt($ch, CURLOPT_VERBOSE, ($this->verbose ? 1 : 0));
         curl_setopt($ch, CURLOPT_HTTPHEADER, $reqHeaders);
         curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
         if ($this->timeout !== false) curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

         $result = curl_exec($ch);
         $info   = curl_getinfo($ch);
         $errmsg = curl_error($ch);
         $errnum = (int) curl_errno($ch);
         curl_close($ch);
      
      } else {
      
         $fp = @fsockopen($host, $this->port, $errnum, $errmsg, $this->timeout);
         if (!$fp) {
            $errorMsg = 'Unable to connect to Shipping Rate Web Service';
            return false;
         }

         // keep track of how long the communications has taken
         $start = time();

         $req  = "POST {$this->uri} HTTP/1.1\r\n";
         $req .= "Host: {$this->url}:{$this->port}\r\n";
         $req .= "Content-type: text/xml\r\n";
         $req .= "User-Agent: {$this->useragent}\r\n";
         $req .= "Content-length: ".strlen($queryData)."\r\n";
         $req .= "Connection: Close\r\n\r\n";

         // Set the timeout for the communications to retreive the results
         stream_set_timeout($fp, $this->timeout, 0);

         if ($this->debug) $this->_debug($req.$queryData, $errmsg, $errnum);
         fwrite($fp, $req.$queryData);
         fflush($fp);

         $result = '';
         $timedOut = false;
         while (!feof($fp)) {
            // See if we've timed out for the communications
            if (time() - $start > $this->timeout) {
               $timedOut = true;
               break;
            }
            $result .= fgets($fp, 1024);
         }
         fclose($fp);
         if ($this->debug) $this->_debug($result, $errmsg, $errnum);
 
         if ($timedOut) {
            $errorMsg = "Communications to rate engine timed out ({$this->timeout} seconds)";
            return false;
         }
      }
      
      // ------------------------------------------------------------------
      // Handle Non 200 (OK) 302 (REDIRECT) Response
      // ------------------------------------------------------------------
      if ($errnum != 0 || !preg_match("#HTTP/1.1 200 OK#i",$result)) {
         $errorMsg = 'An unexpected error occured while communicating with Shipping Rate Web Service';
         return(false);
      }
    
      if (strstr($result, "\r\n\r\n")) {  
         // loop to handle "HTTP/1.1 100 Continue" headers
         $headers = '';
         while(true) {
            list($respHeaders, $respContent) = preg_split("/\r\n\r\n/",$result,2);
            
            // See if we got a 100 Continue header
            if (preg_match('/^HTTP\/1\.[0-9][ ]{1,}100[ ]{1,}/', $respHeaders)) {
               // Hold onto the continue header
               $headers .= $respHeaders;
               $result = $respContent;
               continue;
            }
            break;
         }
         // Tack the headers back together if we had multiple.
         if (isset($headers)) {
            $respHeaders = $headers . $respHeaders;
         }
      } elseif (stristr($result, 'content-length: 0')) {
         $respHeaders = $result;
         $respContent = '';         
      } else {
         $respContent = $result;      
      }
      return(true);
   }

   ##########################################################################
   ### Debug Methods
   ##########################################################################

   function _debug($result,$errmsg,$errnum)
   {
      echo "<hr><pre>".htmlspecialchars(print_r($result,1))."</pre>";
      if ($errnum) echo "<hr>Connection Error [$errnum]:<pre>".htmlspecialchars($errmsg)."</pre>\n";
   }

   ##########################################################################
   ### Unit Testing
   ##########################################################################

   function _test()
   {
      $result = '';
      // $url  = "http://localhost/~victord/postServer.php";
      $data = "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?><test>Hello World</test>\n";
      $data = implode('',file("_ss.xml"));
      $post = new ShipRateSocketPost();
      $post->setDebug(false);
      if ($post->post($data,false,$out,$headers)) {
         echo "--------- SUCCESS ---------\n\n$headers\n\n$out\n";
      } else {
         echo "--------- FAILURE ---------\n\n$headers\n\n$out\n";
      }

      echo "\n\n";

      $p = new ShipRateParserXMLItem();
      $data = $p->parse($out);
      print_r($data);
      echo "\n";
   }
}

?>
