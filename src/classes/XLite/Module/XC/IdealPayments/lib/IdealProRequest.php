<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

/**
 * Service class to communicate with iDEAL payment gateway
 */
class IdealProRequest /* v.3.3.1 */ {

    protected $aErrors = array();
    // Security settings
    protected $sSecurePath;
    protected $sCachePath;
    protected $sPrivateKeyPass;
    protected $sPrivateKeyFile;
    protected $sPrivateCertificateFile;
    protected $sPublicCertificateFile;
    // Account settings
    protected $bABNAMRO = false; // ABN has some issues
    protected $sAquirerUrl;
    protected $bTestMode = false;
    protected $sMerchantId;
    protected $sSubId;
    // Constants
    protected $LF = "\n";
    protected $CRLF = "\r\n";

    /**
     * Constructor
     */
    public function __construct($params)
    {
        // Set up Merchant ID and SubID values
        $this->setMerchant($params['merchant_id'], $params['subid']);

        // Set up private/public keys and private key password values
        $this->setPrivateKey($params['private_key_pass'], $params['private_key'], $params['pub_key']);

        // Set up path where located public certificate
        $this->setSecurePath($params['securePath']);

        // Set up public certificate file name
        $this->sPublicCertificateFile = $params['pub_cert'];

        // Set up test mode (Y/N)
        $this->setMode($params['test']);

        // Set up path where will be located cache of issuers
        $this->setCachePath($params['cachePath']);
    }

    /**
     * Set secure path
     * 
     * Should point to directory with .cer and .key files
     * 
     * @param string $sPath
     */
    public function setSecurePath($sPath)
    {
        $this->sSecurePath = $sPath;
    }

    /**
     * Set cache path
     * 
     * @param string $sPath
     */
    public function setCachePath($sPath = false)
    {
        $this->sCachePath = $sPath;
    }

    /**
     * Set private key
     * 
     * @param string $sPrivateKeyPass
     * @param string $sPrivateKeyFile
     * @param string $sPrivateCertificateFile
     */
    public function setPrivateKey($sPrivateKeyPass, $sPrivateKeyFile = false, $sPrivateCertificateFile = false)
    {
        $this->sPrivateKeyPass = $sPrivateKeyPass;

        if ($sPrivateKeyFile) {
            $this->sPrivateKeyFile = $sPrivateKeyFile;
        }

        if ($sPrivateCertificateFile) {
            $this->sPrivateCertificateFile = $sPrivateCertificateFile;
        }
    }

    /**
     * Set MerchantID id and SubID
     * 
     * @param string $sMerchantId
     * @param string $sSubId
     */
    public function setMerchant($sMerchantId, $sSubId = 0)
    {
        $this->sMerchantId = $sMerchantId;
        $this->sSubId = $sSubId;
    }

    /**
     * Set mode (Test, Live)
     * 
     * @param string $sMode
     */
    public function setMode($sMode)
    {
        if (in_array($sMode, array('Y', 'N'))) {
            $this->bTestMode = ($sMode == 'Y');
            $this->sAquirerUrl = 'https://ideal' . ($this->bTestMode ? 'test' : '') . '.rabobank.nl:443/ideal/iDEALv3';
        }
    }

    /**
     * Check private and public keys
     */
    public function checkSignature()
    {
        $this->getSignature('test', $this->sPrivateKeyFile, $this->sPrivateKeyPass);
        $this->getCertificateFingerprint($this->sPrivateCertificateFile);
    }

    /**
     * Fatal error handler
     * 
     * @param string $sMessage
     */
    protected static function idealcheckout_die($sMessage, $sFile = '', $nLine = 0)
    {
        \XLite\Module\XC\IdealPayments\Main::addLog($sMessage . " in $sFile:$nLine");

        throw new \Exception($sMessage);
    }

    /**
     * Set Error message
     * 
     * @param type $sDesc
     * @param type $sCode
     * @param type $sFile
     * @param type $sLine
     */
    protected function setError($sDesc, $sCode = false, $sFile = 0, $sLine = 0)
    {
        $this->aErrors[] = array('desc' => $sDesc, 'code' => $sCode, 'file' => $sFile, 'line' => $sLine);
    }

    /**
     * Get errors
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->aErrors;
    }

    /**
     * Has errors
     * 
     * @return boolean
     */
    public function hasErrors()
    {
        return (sizeof($this->aErrors) ? true : false);
    }

    /**
     * Validate configuration
     * 
     * @param array $aSettings
     * 
     * @return boolean
     */
    protected function checkConfiguration($aSettings = array('sSecurePath', 'sPrivateKeyPass', 'sPrivateKeyFile', 'sPrivateCertificateFile', 'sPublicCertificateFile', 'sAquirerUrl', 'sMerchantId'))
    {
        $bOk = true;

        for ($i = 0; $i < sizeof($aSettings); $i++) {
            if (isset($this->{$aSettings[$i]}) == false) {
                $bOk = false;
                $this->setError('Setting ' . $aSettings[$i] . ' was not configurated.', false, __FILE__, __LINE__);
            }
        }

        return $bOk;
    }

    /**
     * Send GET/POST data through sockets
     * 
     * @param string $url
     * @param string $data
     * @param integer $timeout
     * 
     * @return string
     */
    protected function postToHost($url, $data)
    {
        $request = new \XLite\Core\HTTP\Request($url);
        $request->body = $data;

        $response = $request->sendRequest();

        // Log request/response
        \XLite\Module\XC\IdealPayments\Main::addLog(
            'Post request and response',
            array(
                'request' => array(
                    'url' => $url,
                    'data' => $data
                ),
                'response' => '200' == $response->code ? $response->body : $response
            )
        );

        $result = '';
        
        if ($response->code != '200') {
            $this->setError('Error while connecting to ' . $url, false, __FILE__, __LINE__);

        } else {
            $result = $response->body;
        }
        
        return $result;
    }

    /**
     * Get value within given XML tag
     * 
     * @param string $key
     * @param string $xml
     * 
     * @return boolean
     */
    protected function parseFromXml($key, $xml)
    {
        $begin = 0;
        $end = 0;
        $begin = strpos($xml, '<' . $key . '>');

        if ($begin === false) {
            return false;
        }

        $begin += strlen($key) + 2;
        $end = strpos($xml, '</' . $key . '>');

        if ($end === false) {
            return false;
        }

        $result = substr($xml, $begin, $end - $begin);
        
        return $this->unescapeXml($result);
    }

    /**
     * Remove space characters from string
     * 
     * @param type $string
     * 
     * @return string
     */
    protected function removeSpaceCharacters($string)
    {
        return preg_replace('/\s/', '', $string);
    }

    /**
     * Escape (replace/remove) special characters in string
     * 
     * @param string $string
     * 
     * @return string
     */
    protected function escapeSpecialChars($string)
    {
        $string = str_replace(array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ð', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', '§', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', '€', 'Ð', 'Ì', 'Í', 'Î', 'Ï', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', '§', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Ÿ'), array('a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'ed', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 's', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'EUR', 'ED', 'I', 'I', 'I', 'I', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'S', 'U', 'U', 'U', 'U', 'Y', 'Y'), $string);
        $string = preg_replace('/[^a-zA-Z0-9\-\.\,\(\)_]+/', ' ', $string);
        $string = preg_replace('/[\s]+/', ' ', $string);

        return $string;
    }

    /**
     * Escape special XML characters
     * 
     * @param string $string
     * 
     * @return string
     */
    protected function escapeXml($string)
    {
        return utf8_encode(str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
    }

    /**
     * Unescape special XML characters
     * 
     * @param string $string
     * 
     * @return string
     */
    protected function unescapeXml($string)
    {
        return str_replace(array('&lt;', '&gt;', '&quot;', '&amp;'), array('<', '>', '"', '&'), utf8_decode($string));
    }

    /**
     * Get Message digest signature
     * 
     * @param string $sMessage
     * 
     * @return string
     */
    protected function getMessageDigest($sMessage)
    {
        return base64_encode(hash('sha256', $sMessage, true));
    }

    /**
     * Get signature
     * 
     * @param string $sMessage
     * @param string $sKeyPath
     * @param string $sKeyPassword
     * 
     * @return string
     */
    protected function getSignatureFromFile($sMessage, $sKeyPath, $sKeyPassword = false)
    {
        $sKeyData = file_get_contents($sKeyPath);

        return $this->getSignature($sMessage, $sKeyData, $sKeyPassword);
    }

    /**
     * Get signature
     * 
     * @param string $sMessage
     * @param string $sKeyPath
     * @param string $sKeyPassword
     * 
     * @return string
     */
    protected function getSignature($sMessage, $sKeyData, $sKeyPassword = false)
    {
        if ($sKeyPassword === false) {
            $oKeyData = openssl_get_publickey($sKeyData);
        } else {
            $oKeyData = openssl_get_privatekey($sKeyData, $sKeyPassword);
        }

        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            if (!self::openssl_sign_alternative($sMessage, $sSignature, $oKeyData)) {
                self::idealcheckout_die('Cannot sign message', __FILE__, __LINE__);
            }
        } else {
            if (!@openssl_sign($sMessage, $sSignature, $oKeyData, 'SHA256')) {
                self::idealcheckout_die('Cannot sign message', __FILE__, __LINE__);
            }
        }

        $sSignature = base64_encode($sSignature);

        return $sSignature;
    }

    /**
     * Verify signature
     * 
     * @param string $sMessage
     * @param string $sSignature
     * @param string $sCertificatePath
     * 
     * @return string
     */
    protected function verifySignature($sMessage, $sSignature, $sCertificatePath)
    {
        $sCertificateData = file_get_contents($sCertificatePath);
        $oCertificateData = openssl_get_publickey($sCertificateData);

        // Replace self-closing-tags
        $sMessage = str_replace(array('/><SignatureMethod', '/><Reference', '/></Transforms', '/><DigestValue'), array('></CanonicalizationMethod><SignatureMethod', '></SignatureMethod><Reference', '></Transform></Transforms', '></DigestMethod><DigestValue'), $sMessage);

        // Decode signature
        $sSignature = base64_decode($sSignature);

        if (version_compare(PHP_VERSION, '5.3.0') < 0) {
            return self::openssl_verify_alternative($sMessage, $sSignature, $oCertificateData);
        } else {
            return openssl_verify($sMessage, $sSignature, $oCertificateData, 'SHA256');
        }
    }

    /**
     * Verify Digest signature
     * 
     * @param string $sMessage
     * @param string $sDigest
     * 
     * @return boolean
     */
    protected function verifyDigest($sMessage, $sDigest)
    {
        return (strcmp($this->getMessageDigest($sMessage), $sDigest) === 0);
    }

    /**
     * Get certificate fingerprint from file
     * 
     * @param string $sFilePath
     * 
     * @return string
     */
    protected function getCertificateFingerprintFromFile($sFilePath)
    {
        if (!$sFilePath || !is_file($sFilePath)) {
            self::idealcheckout_die('Invalid certificate file: ' . $sFilePath . '.', __FILE__, __LINE__);
        }

        $sData = file_get_contents($sFilePath);

        if (empty($sData)) {
            self::idealcheckout_die('Invalid certificate file: ' . $sFilePath . '.', __FILE__, __LINE__);
        }

        return $this->getCertificateFingerprint($sData);
    }

    /**
     * Get certificate fingerprint
     * 
     * @param string $sFilePath
     * 
     * @return string
     */
    protected function getCertificateFingerprint($sData)
    {
        $oData = openssl_x509_read($sData);

        if ($oData == false) {
            self::idealcheckout_die('Invalid certificate file: ' . $sFilePath . '.', __FILE__, __LINE__);
        } elseif (!openssl_x509_export($oData, $sData)) {
            self::idealcheckout_die('Invalid certificate file: ' . $sFilePath . '.', __FILE__, __LINE__);
        }

        // Remove any ASCII armor
        $sData = str_replace('-----BEGIN CERTIFICATE-----', '', $sData);
        $sData = str_replace('-----END CERTIFICATE-----', '', $sData);
        
        $sFingerprint = strtoupper(sha1(base64_decode($sData)));

        return $sFingerprint;
    }


    /**
     * Get public certificate file
     * 
     * @param type $sCertificateFingerprint
     * 
     * @return boolean
     */
    protected function getPublicCertificateFile($sCertificateFingerprint)
    {
        $aCertificateFiles = array();

        if (file_exists($this->sSecurePath . $this->sPublicCertificateFile)) {
            $aCertificateFiles[] = $this->sPublicCertificateFile;
        }

        // Upto 10 public certificates by acquirer; eg: rabobank-0.cer, rabobank-1.cer, rabobank-2.cer, etc.
        for ($i = 0; $i < 10; $i++) {
            $sCertificateFile = substr($this->sPublicCertificateFile, 0, -4) . '-' . $i . '.cer';

            if (file_exists($this->sSecurePath . $sCertificateFile)) {
                $aCertificateFiles[] = $sCertificateFile;
            }
        }
        
        // Test each certificate with given fingerprint
        foreach ($aCertificateFiles as $sCertificateFile) {
            $sFingerprint = $this->getCertificateFingerprintFromFile($this->sSecurePath . $sCertificateFile);

            if (strcmp($sFingerprint, $sCertificateFingerprint) === 0) {
                return $this->sSecurePath . $sCertificateFile;
            }
        }

        return false;
    }

    /**
     * Verify response message (<DigestValue>, <SignatureValue>)
     * 
     * @param string $sXmlData
     * @param string $sResponseType
     * 
     * @return boolean
     */
    protected function verifyResponse($sXmlData, $sResponseType)
    {
        $sCertificateFingerprint = $this->parseFromXml('KeyName', $sXmlData);
        $sDigestValue = $this->parseFromXml('DigestValue', $sXmlData);
        $sSignatureValue = str_replace(array("\r", "\n"), '', $this->parseFromXml('SignatureValue', $sXmlData));

        $sDigestData = '';

        if ($this->parseFromXml('errorCode', $sXmlData)) { // Error found
            // Add error to error-list
            $this->setError($this->parseFromXml('errorMessage', $sXmlData) . ' - ' . $this->parseFromXml('errorDetail', $sXmlData), $this->parseFromXml('errorCode', $sXmlData), __FILE__, __LINE__);
        } elseif (strpos($sXmlData, '</' . $sResponseType . '>') !== false) { // Directory Response
            // Strip <Signature>
            $iStart = strpos($sXmlData, '<' . $sResponseType);
            $iEnd = strpos($sXmlData, '<Signature');
            $sDigestData = substr($sXmlData, $iStart, $iEnd - $iStart) . '</' . $sResponseType . '>';
        }

        if (!empty($sDigestData)) {
            // Recalculate & compare DigestValue
            if ($this->verifyDigest($sDigestData, $sDigestValue)) {
                // Find <SignedInfo>, and add ' xmlns="http://www.w3.org/2000/09/xmldsig#"'
                $iStart = strpos($sXmlData, '<SignedInfo>');
                $iEnd = strpos($sXmlData, '</SignedInfo>');
                $sSignatureData = '<SignedInfo xmlns="http://www.w3.org/2000/09/xmldsig#">' . substr($sXmlData, $iStart + 12, $iEnd - ($iStart + 12)) . '</SignedInfo>';

                if (!empty($sSignatureData)) {
                    // Detect used public certificate by given fingerprint
                    if ($sPublicCertificateFile = $this->getPublicCertificateFile($sCertificateFingerprint)) {
                        // Recalculate & compare SignatureValue
                        if ($this->verifySignature($sSignatureData, $sSignatureValue, $sPublicCertificateFile)) {
                            return true;
                        } else {
                            $this->setError('Invalid signature value in XML response.', '', __FILE__, __LINE__);
                        }
                    } else {
                        $this->setError('Cannot find public certificate file with fingerprint: ' . $sCertificateFingerprint, '', __FILE__, __LINE__);
                    }
                } else {
                    $this->setError('Cannot find <SignedInfo> in XML response.', '', __FILE__, __LINE__);
                }
            } else {
                $this->setError('Invalid digest value in XML response.', '', __FILE__, __LINE__);
            }
        } else {
            $this->setError('Cannot find <' . $sResponseType . '> in XML response.', '', __FILE__, __LINE__);
        }

        return false;
    }

    // PHP 5.2 alternative for SHA256 signing
    public static function openssl_sign_alternative($sMessage, &$sSignature, $oKeyData)
    {
        $aPrivateKey = openssl_pkey_get_details($oKeyData);

        $sSha256 = '3031300d060960864801650304020105000420';
        $sHash = $sSha256 . hash('sha256', $sMessage);

        $iLength = ($aPrivateKey['bits'] / 8) - ((strlen($sHash) / 2) + 3);

        $sData = '0001' . str_repeat('FF', $iLength) . '00' . $sHash;
        $sData = pack('H*', $sData);

        return openssl_private_encrypt($sData, $sSignature, $oKeyData, OPENSSL_NO_PADDING);
    }

    // PHP 5.2 alternative for SHA256 validation
    public static function openssl_verify_alternative($sMessage, &$sSignature, $oKeyData)
    {
        $aPrivateKey = openssl_pkey_get_details($oKeyData);

        $sSha256 = '3031300d060960864801650304020105000420';
        $sHash = $sSha256 . hash('sha256', $sMessage);

        $iLength = ($aPrivateKey['bits'] / 8) - ((strlen($sHash) / 2) + 3);

        $sData = '0001' . str_repeat('FF', $iLength) . '00' . $sHash;
        $sData = pack('H*', $sData);

        return openssl_public_decrypt($sData, $sSignature, $oKeyData, OPENSSL_NO_PADDING);
    }

}

class IdealProIssuerRequest extends IdealProRequest {

    /**
     * Execute request (Lookup issuer list)
     * 
     * @return boolean
     */
    public function doRequest()
    {
        if ($this->checkConfiguration()) {
            $sCacheFile = false;

            // Used cached issuers?
            if (($this->bTestMode == true) && $this->sCachePath) {
                $sCacheFile = $this->sCachePath . 'cache_ideal_rb_prof_issuers_';
                $bFileCreated = false;

                if (file_exists($sCacheFile) == false) {
                    $bFileCreated = true;

                    // Attempt to create cache file
                    \Includes\Utils\FileManager::write($sCacheFile, '');
                }

                if (file_exists($sCacheFile) && is_readable($sCacheFile) && is_writable($sCacheFile)) {
                    if ($bFileCreated || (filemtime($sCacheFile) > strtotime('-24 Hours'))) {
                        // Read data from cache file
                        if ($sData = file_get_contents($sCacheFile)) {
                            return unserialize($sData);
                        }
                    }
                } else {
                    $sCacheFile = false;
                }
            }

            $sTimestamp = gmdate('Y-m-d\TH:i:s.000\Z');
            $sCertificateFingerprint = $this->getCertificateFingerprint($this->sPrivateCertificateFile);

            $sXml = '<DirectoryReq xmlns="http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1" version="3.3.1">';
            $sXml .= '<createDateTimestamp>' . $sTimestamp . '</createDateTimestamp>';
            $sXml .= '<Merchant>';
            $sXml .= '<merchantID>' . $this->sMerchantId . '</merchantID>';
            $sXml .= '<subID>' . $this->sSubId . '</subID>';
            $sXml .= '</Merchant>';
            $sXml .= '</DirectoryReq>';

            // Calculate <DigestValue>
            $sDigestValue = $this->getMessageDigest($sXml);

            $sXml = '<SignedInfo xmlns="http://www.w3.org/2000/09/xmldsig#">';
            $sXml .= '<CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></CanonicalizationMethod>';
            $sXml .= '<SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></SignatureMethod>';
            $sXml .= '<Reference URI="">';
            $sXml .= '<Transforms>';
            $sXml .= '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></Transform>';
            $sXml .= '</Transforms>';
            $sXml .= '<DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></DigestMethod>';
            $sXml .= '<DigestValue>' . $sDigestValue . '</DigestValue>';
            $sXml .= '</Reference>';
            $sXml .= '</SignedInfo>';

            // Calculate <SignatureValue>
            $sSignatureValue = $this->getSignature($sXml, $this->sPrivateKeyFile, $this->sPrivateKeyPass);

            $sXml = '<' . '?' . 'xml version="1.0" encoding="UTF-8"' . '?' . '>' . "\n";
            $sXml .= '<DirectoryReq xmlns="http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1" version="3.3.1">';
            $sXml .= '<createDateTimestamp>' . $sTimestamp . '</createDateTimestamp>';
            $sXml .= '<Merchant>';
            $sXml .= '<merchantID>' . $this->sMerchantId . '</merchantID>';
            $sXml .= '<subID>' . $this->sSubId . '</subID>';
            $sXml .= '</Merchant>';
            $sXml .= '<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">';
            $sXml .= '<SignedInfo>';
            $sXml .= '<CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></CanonicalizationMethod>';
            $sXml .= '<SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></SignatureMethod>';
            $sXml .= '<Reference URI="">';
            $sXml .= '<Transforms>';
            $sXml .= '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></Transform>';
            $sXml .= '</Transforms>';
            $sXml .= '<DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></DigestMethod>';
            $sXml .= '<DigestValue>' . $sDigestValue . '</DigestValue>';
            $sXml .= '</Reference>';
            $sXml .= '</SignedInfo>';
            $sXml .= '<SignatureValue>' . $sSignatureValue . '</SignatureValue>';
            $sXml .= '<KeyInfo>';
            $sXml .= '<KeyName>' . $sCertificateFingerprint . '</KeyName>';
            $sXml .= '</KeyInfo>';
            $sXml .= '</Signature>';
            $sXml .= '</DirectoryReq>';

            $sXmlReply = $this->postToHost($this->sAquirerUrl, $sXml);

            if ($sXmlReply) {
                if ($this->verifyResponse($sXmlReply, 'DirectoryRes')) {
                    $aIssuerList = array();

                    while (strpos($sXmlReply, '<issuerID>')) {
                        $sIssuerId = $this->parseFromXml('issuerID', $sXmlReply);
                        $sIssuerName = $this->parseFromXml('issuerName', $sXmlReply);

                        $aIssuerList[$sIssuerId] = $sIssuerName;

                        $sXmlReply = substr($sXmlReply, strpos($sXmlReply, '</Issuer>') + 9);
                    }

                    // Save data in cache?
                    if ($sCacheFile) {
                        file_put_contents($sCacheFile, serialize($aIssuerList));
                    }

                    return $aIssuerList;
                }
            }
        }

        return false;
    }

}

class IdealProTransactionRequest extends IdealProRequest {

    // Order info
    protected $sOrderId;
    protected $sOrderDescription;
    protected $fOrderAmount;
    protected $sReturnUrl;
    protected $sIssuerId;
    protected $sEntranceCode;
    // Transaction info
    protected $sTransactionId;
    protected $sTransactionUrl;

    public function __construct($params)
    {
        parent::__construct($params);
        
        $this->setReturnUrl($this->escapeXml($params['returnURL']));

        // Random EntranceCode
        $this->sEntranceCode = sha1(rand(1000000, 9999999));
    }

    /**
     * Set OrderId
     * 
     * @param string $sOrderId
     */
    public function setOrderId($sOrderId)
    {
        $this->sOrderId = substr($sOrderId, 0, 16);
    }

    /**
     * Set Order description
     * 
     * @param string $sOrderDescription
     */
    public function setOrderDescription($sOrderDescription)
    {
        $this->sOrderDescription = trim(substr($this->escapeSpecialChars($sOrderDescription), 0, 32));
    }

    /**
     * Set Order amount
     * 
     * @param float $fOrderAmount
     */
    public function setOrderAmount($fOrderAmount)
    {
        $this->fOrderAmount = round($fOrderAmount, 2);
    }

    /**
     * Set Return URL
     * 
     * @param string $sReturnUrl
     */
    public function setReturnUrl($sReturnUrl)
    {
        $this->sReturnUrl = substr($sReturnUrl, 0, 512);
    }

    /**
     * ID of the selected bank
     * 
     * @param string $sIssuerId
     */
    public function setIssuerId($sIssuerId)
    {
        $sIssuerId = preg_replace('/[^a-zA-Z0-9]/', '', $sIssuerId);
        $this->sIssuerId = $sIssuerId;
    }

    /**
     * A random generated entrance code
     * 
     * @param string $sEntranceCode
     */
    public function setEntranceCode($sEntranceCode)
    {
        $this->sEntranceCode = substr($sEntranceCode, 0, 40);
    }

    /**
     * Retrieve the transaction URL recieved in the XML response of de IDEAL SERVER
     * 
     * @return string
     */
    public function getTransactionUrl()
    {
        return $this->sTransactionUrl;
    }

    /**
     * Execute request (Setup transaction)
     * 
     * @return boolean
     */
    public function doRequest()
    {
        if ($this->checkConfiguration() && $this->checkConfiguration(array('sOrderId', 'sOrderDescription', 'fOrderAmount', 'sReturnUrl', 'sReturnUrl', 'sIssuerId', 'sEntranceCode'))) {
            $sTimestamp = gmdate('Y-m-d\TH:i:s.000\Z');
            $sCertificateFingerprint = $this->getCertificateFingerprint($this->sPrivateCertificateFile);

            $sXml = '<AcquirerTrxReq xmlns="http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1" version="3.3.1">';
            $sXml .= '<createDateTimestamp>' . $sTimestamp . '</createDateTimestamp>';
            $sXml .= '<Issuer>';
            $sXml .= '<issuerID>' . $this->sIssuerId . '</issuerID>';
            $sXml .= '</Issuer>';
            $sXml .= '<Merchant>';
            $sXml .= '<merchantID>' . $this->sMerchantId . '</merchantID>';
            $sXml .= '<subID>' . $this->sSubId . '</subID>';
            $sXml .= '<merchantReturnURL>' . $this->sReturnUrl . '</merchantReturnURL>';
            $sXml .= '</Merchant>';
            $sXml .= '<Transaction>';
            $sXml .= '<purchaseID>' . $this->sOrderId . '</purchaseID>';
            $sXml .= '<amount>' . number_format($this->fOrderAmount, 2, '.', '') . '</amount>';
            $sXml .= '<currency>EUR</currency>';
            $sXml .= '<expirationPeriod>PT1H</expirationPeriod>';
            $sXml .= '<language>nl</language>';
            $sXml .= '<description>' . $this->sOrderDescription . '</description>';
            $sXml .= '<entranceCode>' . $this->sEntranceCode . '</entranceCode>';
            $sXml .= '</Transaction>';
            $sXml .= '</AcquirerTrxReq>';

            // Calculate <DigestValue>
            $sDigestValue = $this->getMessageDigest($sXml);

            $sXml = '<SignedInfo xmlns="http://www.w3.org/2000/09/xmldsig#">';
            $sXml .= '<CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></CanonicalizationMethod>';
            $sXml .= '<SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></SignatureMethod>';
            $sXml .= '<Reference URI="">';
            $sXml .= '<Transforms>';
            $sXml .= '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></Transform>';
            $sXml .= '</Transforms>';
            $sXml .= '<DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></DigestMethod>';
            $sXml .= '<DigestValue>' . $sDigestValue . '</DigestValue>';
            $sXml .= '</Reference>';
            $sXml .= '</SignedInfo>';

            // Calculate <SignatureValue>
            $sSignatureValue = $this->getSignature($sXml, $this->sPrivateKeyFile, $this->sPrivateKeyPass);

            $sXml = '<' . '?' . 'xml version="1.0" encoding="UTF-8"' . '?' . '>' . "\n";
            $sXml .= '<AcquirerTrxReq xmlns="http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1" version="3.3.1">';
            $sXml .= '<createDateTimestamp>' . $sTimestamp . '</createDateTimestamp>';
            $sXml .= '<Issuer>';
            $sXml .= '<issuerID>' . $this->sIssuerId . '</issuerID>';
            $sXml .= '</Issuer>';
            $sXml .= '<Merchant>';
            $sXml .= '<merchantID>' . $this->sMerchantId . '</merchantID>';
            $sXml .= '<subID>' . $this->sSubId . '</subID>';
            $sXml .= '<merchantReturnURL>' . $this->sReturnUrl . '</merchantReturnURL>';
            $sXml .= '</Merchant>';
            $sXml .= '<Transaction>';
            $sXml .= '<purchaseID>' . $this->sOrderId . '</purchaseID>';
            $sXml .= '<amount>' . number_format($this->fOrderAmount, 2, '.', '') . '</amount>';
            $sXml .= '<currency>EUR</currency>';
            $sXml .= '<expirationPeriod>PT1H</expirationPeriod>';
            $sXml .= '<language>nl</language>';
            $sXml .= '<description>' . $this->sOrderDescription . '</description>';
            $sXml .= '<entranceCode>' . $this->sEntranceCode . '</entranceCode>';
            $sXml .= '</Transaction>';
            $sXml .= '<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">';
            $sXml .= '<SignedInfo>';
            $sXml .= '<CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></CanonicalizationMethod>';
            $sXml .= '<SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></SignatureMethod>';
            $sXml .= '<Reference URI="">';
            $sXml .= '<Transforms>';
            $sXml .= '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></Transform>';
            $sXml .= '</Transforms>';
            $sXml .= '<DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></DigestMethod>';
            $sXml .= '<DigestValue>' . $sDigestValue . '</DigestValue>';
            $sXml .= '</Reference>';
            $sXml .= '</SignedInfo>';
            $sXml .= '<SignatureValue>' . $sSignatureValue . '</SignatureValue>';
            $sXml .= '<KeyInfo>';
            $sXml .= '<KeyName>' . $sCertificateFingerprint . '</KeyName>';
            $sXml .= '</KeyInfo>';
            $sXml .= '</Signature>';
            $sXml .= '</AcquirerTrxReq>';

            $sXmlReply = $this->postToHost($this->sAquirerUrl, $sXml);

            if ($sXmlReply) {
                if ($this->verifyResponse($sXmlReply, 'AcquirerTrxRes')) {
                    $this->sTransactionId = $this->parseFromXml('transactionID', $sXmlReply);
                    $this->sTransactionUrl = html_entity_decode($this->parseFromXml('issuerAuthenticationURL', $sXmlReply));

                    return $this->sTransactionId;
                }
            }
        }

        return false;
    }

    /**
     * Start transaction
     * 
     * @return boolean
     */
    public function doTransaction()
    {
        if ((sizeof($this->aErrors) == 0) && $this->sTransactionId && $this->sTransactionUrl) {
            header('Location: ' . $this->sTransactionUrl);
            exit;
        }

        $this->setError('Please setup a valid transaction request first.', false, __FILE__, __LINE__);
        return false;
    }

}

class IdealProStatusRequest extends IdealProRequest {

    // Account info
    protected $sAccountCity;
    protected $sAccountName;
    protected $sAccountNumber;
    // Transaction info
    protected $sTransactionId;
    protected $sTransactionStatus;

    /**
     * Set transaction id
     * 
     * @param string $sTransactionId
     */
    public function setTransactionId($sTransactionId)
    {
        $this->sTransactionId = $sTransactionId;
    }

    /**
     * Get account city
     * 
     * @return string
     */
    public function getAccountCity()
    {
        if (!empty($this->sAccountCity)) {
            return $this->sAccountCity;
        }

        return '';
    }

    /**
     * Get account name
     * 
     * @return string
     */
    public function getAccountName()
    {
        if (!empty($this->sAccountName)) {
            return $this->sAccountName;
        }

        return '';
    }

    /**
     * Get account number
     * 
     * @return string
     */
    public function getAccountNumber()
    {
        if (!empty($this->sAccountNumber)) {
            return $this->sAccountNumber;
        }

        return '';
    }

    /**
     * Execute request
     * 
     * @return boolean
     */
    public function doRequest()
    {
        if ($this->checkConfiguration() && $this->checkConfiguration(array('sTransactionId'))) {
            $sTimestamp = gmdate('Y-m-d\TH:i:s.000\Z');
            $sCertificateFingerprint = $this->getCertificateFingerprint($this->sPrivateCertificateFile);

            $sXml = '<AcquirerStatusReq xmlns="http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1" version="3.3.1">';
            $sXml .= '<createDateTimestamp>' . $sTimestamp . '</createDateTimestamp>';
            $sXml .= '<Merchant>';
            $sXml .= '<merchantID>' . $this->sMerchantId . '</merchantID>';
            $sXml .= '<subID>' . $this->sSubId . '</subID>';
            $sXml .= '</Merchant>';
            $sXml .= '<Transaction>';
            $sXml .= '<transactionID>' . $this->sTransactionId . '</transactionID>';
            $sXml .= '</Transaction>';
            $sXml .= '</AcquirerStatusReq>';

            // Calculate <DigestValue>
            $sDigestValue = $this->getMessageDigest($sXml);

            $sXml = '<SignedInfo xmlns="http://www.w3.org/2000/09/xmldsig#">';
            $sXml .= '<CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></CanonicalizationMethod>';
            $sXml .= '<SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></SignatureMethod>';
            $sXml .= '<Reference URI="">';
            $sXml .= '<Transforms>';
            $sXml .= '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></Transform>';
            $sXml .= '</Transforms>';
            $sXml .= '<DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></DigestMethod>';
            $sXml .= '<DigestValue>' . $sDigestValue . '</DigestValue>';
            $sXml .= '</Reference>';
            $sXml .= '</SignedInfo>';

            // Calculate <SignatureValue>
            $sSignatureValue = $this->getSignature($sXml, $this->sPrivateKeyFile, $this->sPrivateKeyPass);

            $sXml = '<' . '?' . 'xml version="1.0" encoding="UTF-8"' . '?' . '>' . "\n";
            $sXml .= '<AcquirerStatusReq xmlns="http://www.idealdesk.com/ideal/messages/mer-acq/3.3.1" version="3.3.1">';
            $sXml .= '<createDateTimestamp>' . $sTimestamp . '</createDateTimestamp>';
            $sXml .= '<Merchant>';
            $sXml .= '<merchantID>' . $this->sMerchantId . '</merchantID>';
            $sXml .= '<subID>' . $this->sSubId . '</subID>';
            $sXml .= '</Merchant>';
            $sXml .= '<Transaction>';
            $sXml .= '<transactionID>' . $this->sTransactionId . '</transactionID>';
            $sXml .= '</Transaction>';
            $sXml .= '<Signature xmlns="http://www.w3.org/2000/09/xmldsig#">';
            $sXml .= '<SignedInfo>';
            $sXml .= '<CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"></CanonicalizationMethod>';
            $sXml .= '<SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#rsa-sha256"></SignatureMethod>';
            $sXml .= '<Reference URI="">';
            $sXml .= '<Transforms>';
            $sXml .= '<Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"></Transform>';
            $sXml .= '</Transforms>';
            $sXml .= '<DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"></DigestMethod>';
            $sXml .= '<DigestValue>' . $sDigestValue . '</DigestValue>';
            $sXml .= '</Reference>';
            $sXml .= '</SignedInfo>';
            $sXml .= '<SignatureValue>' . $sSignatureValue . '</SignatureValue>';
            $sXml .= '<KeyInfo>';
            $sXml .= '<KeyName>' . $sCertificateFingerprint . '</KeyName>';
            $sXml .= '</KeyInfo>';
            $sXml .= '</Signature>';
            $sXml .= '</AcquirerStatusReq>';

            $sXmlReply = $this->postToHost($this->sAquirerUrl, $sXml);

            if ($sXmlReply) {
                // Verify message (DigestValue & SignatureValue)
                if ($this->verifyResponse($sXmlReply, 'AcquirerStatusRes')) {
                    $sTimestamp = $this->parseFromXml('createDateTimeStamp', $sXmlReply);
                    $sTransactionId = $this->parseFromXml('transactionID', $sXmlReply);
                    $sTransactionStatus = $this->parseFromXml('status', $sXmlReply);

                    // Try to keep field compatible where possible
                    $sAccountNumber = $this->parseFromXml('consumerIBAN', $sXmlReply) . ' | ' . $this->parseFromXml('consumerBIC', $sXmlReply);
                    $sAccountName = $this->parseFromXml('consumerName', $sXmlReply);
                    $sAccountCity = '-';

                    // $this->sTransactionId = $sTransactionId;
                    $this->sTransactionStatus = strtoupper($sTransactionStatus);

                    $this->sAccountCity = $sAccountCity;
                    $this->sAccountName = $sAccountName;
                    $this->sAccountNumber = $sAccountNumber;

                    return $this->sTransactionStatus;
                }
            }
        }

        return false;
    }

}

?>
