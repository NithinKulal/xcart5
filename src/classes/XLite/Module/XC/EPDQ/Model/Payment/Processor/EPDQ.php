<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\EPDQ\Model\Payment\Processor;

/**
 * Barclaycard ePDQ e-Commerce Basic (Essential) integration (v.3.3)
 *
 * Find the latest API document here:
 * https://mdepayments.epdq.co.uk/ncol/ePDQ_e-COM-BAS_EN.pdf
 * (http://www.barclaycard.co.uk/business/accepting-payments/e-commerce-services-for-sme/epdq#tabbox3)
 */
class EPDQ extends \XLite\Model\Payment\Base\WebBased
{
    /**
     * ePDQ initialize transaction request TTL
     */
    const EPDQ_INIT_REQUEST_TTL = 5;

    /**
     * Prefix for ePDQ log file name
     */
    const EPDQ_LOG_TYPE = 'epdq-cpi';


    /**
     * Get operation types
     *
     * @return array
     */
    public function getOperationTypes()
    {
        return array(
            self::OPERATION_SALE,
        );
    }

    /**
     * Get settings widget or template
     *
     * @return string Widget class name or template path
     */
    public function getSettingsWidget()
    {
        return 'modules/XC/EPDQ/config.twig';
    }

    /**
     * Process return
     *
     * @param \XLite\Model\Payment\Transaction $transaction Return-owner transaction
     *
     * @return void
     */
    public function processReturn(\XLite\Model\Payment\Transaction $transaction)
    {
        parent::processReturn($transaction);

        \XLite\Module\XC\EPDQ\Main::addLog(
            'processReturn',
            \XLite\Core\Request::getInstance()->getData()
        );

        $response = array_change_key_case(\XLite\Core\Request::getInstance()->getData(), CASE_UPPER);

        $status = $transaction::STATUS_FAILED;

        $this->saveDataFromRequest();

        if (\XLite\Core\Request::getInstance()->cancel) {

            // Cancel

            $this->setDetail(
                'status',
                'Customer has canceled checkout before completing their payments',
                'Status'
            );
            $this->transaction->setNote('Customer has canceled checkout before completing their payments');
            $status = $transaction::STATUS_CANCELED;

        } else {

            // Accept / Decline

            $message = '';

            if (!$this->isResponseTrusted($response)) {
                $message = static::t('Response from ePDQ is not trusted (SHA checking is failed)');

            } elseif (!isset($response['STATUS'])) {
                $message = static::t('Unexpected result was received from ePDQ (transaction status is not set)');

            } else {

                $allowedStatuses = $this->getEPDQStatuses();

                $this->setDetail(
                    'status',
                    isset($allowedStatuses[$response['STATUS']]) ? $allowedStatuses[$response['STATUS']] : 'Unknown status received',
                    'Status'
                );

                if (in_array($response['STATUS'], $this->getSuccessfulStatuses())) {
            
                    if (!$this->checkTotal($response['AMOUNT'])) {
                        $this->setDetail('StatusDetail', 'Invalid amount value was received. Transaction rejected.', 'Status details');
                        $status = $transaction::STATUS_FAILED;

                    } else {
                        $status = $transaction::STATUS_SUCCESS;
                    }
                }
            }

            if ($message) {
                $this->setDetail('status', $message, 'Status');
            }
        }

        $this->transaction->setStatus($status);
    }

    /**
     * Get initial transaction type (used when customer places order)
     *
     * @param \XLite\Model\Payment\Method $method Payment method object OPTIONAL
     *
     * @return string
     */
    public function getInitialTransactionType($method = null)
    {
        return \XLite\Model\Payment\BackendTransaction::TRAN_TYPE_SALE;
    }

    /**
     * Check - payment method is configured or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isConfigured(\XLite\Model\Payment\Method $method)
    {
        return parent::isConfigured($method)
            && $method->getSetting('pspid')
            && $method->getSetting('sha_in')
            && $method->getSetting('sha_out')
            && $method->getSetting('currency');
    }

    /**
     * Get return type
     *
     * @return string
     */
    public function getReturnType()
    {
        return self::RETURN_TYPE_HTML_REDIRECT;
    }

    /**
     * Returns the list of settings available for this payment processor
     *
     * @return array
     */
    public function getAvailableSettings()
    {
        return array(
            'pspid',
            'sha_in',
            'sha_out',
            'currency',
            'prefix',
            'test',
            'debug_enabled',
        );
    }

    /**
     * Get payment method admin zone icon URL
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return string
     */
    public function getAdminIconURL(\XLite\Model\Payment\Method $method)
    {
        return true;
    }

    /**
     * Check - payment method has enabled test mode or not
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @return boolean
     */
    public function isTestMode(\XLite\Model\Payment\Method $method)
    {
        return (bool)$this->getSetting('test');
    }

    /**
     * Get redirect form URL
     *
     * @return string
     */
    protected function getFormURL()
    {
        return $this->getSetting('test')
            ? 'https://mdepayments.epdq.co.uk/ncol/test/orderstandard.asp'
            : 'https://payments.epdq.co.uk/ncol/prod/orderstandard.asp';
    }

    /**
     * Get redirect form fields list
     *
     * @return array
     */
    protected function getFormFields()
    {
        $currentLng = \XLite\Core\Session::getInstance()->getLanguage();

        if ($currentLng) {
            $lngCode = $currentLng->getCode();

        } else {
            $lngCode = 'US';
        }

        $customerName = implode(
            ' ',
             array(
                $this->getProfile()->getBillingAddress()->getFirstname(),
                $this->getProfile()->getBillingAddress()->getLastname()
            )
        );

        $fields = array(
            // Affiliation name in system
            'PSPID'         => $this->getSetting('pspid'),
            // Order number
            'ORDERID'       => $this->getTransactionId(),
            // Amount to be paid
            // MULTIPLIED BY 100 since the format of the amount must not contain any decimals or other separators
            'AMOUNT'        => round($this->transaction->getValue(), 2) * 100,
            // ISO alpha code
            'CURRENCY'      => $this->getSetting('currency'),
            // en_US, nl_NL, fr_FR, …
            'LANGUAGE'      => strtolower($lngCode) . '_' . strtoupper($lngCode),
            // Merchant’s homepage URL
            'HOMEURL'       => \XLite::getInstance()->getShopURL(),
            // Customer name
            'CN'            => $customerName,
            'EMAIL'         => $this->getProfile()->getLogin(),
            'OWNERZIP'      => $this->getProfile()->getBillingAddress()->getZipcode(),
            'OWNERADDRESS'  => $this->getProfile()->getBillingAddress()->getStreet(),
            'OWNERCTY'      => $this->getProfile()->getBillingAddress()->getCountry()->getCode(),
            'OWNERTOWN'     => $this->getProfile()->getBillingAddress()->getCity(),

            // URL of the web page to show the customer when the payment is authorised.
            'ACCEPTURL' => $this->getReturnURL(null, true),
            // URL of the web page to show the customer when the acquirer rejects the authorisation more than
            // the maximum of authorised tries (10 by default, but can be changed in the technical information page).
            'DECLINEURL' => $this->getReturnURL(null, true),
            // URL of the web page to show the customer when the payment result is uncertain.
            'EXCEPTIONURL' => $this->getReturnURL(null, true),
            // URL of the web page to show the customer when he cancels the payment.
            'CANCELURL' => $this->getReturnURL(null, true, true),
        );

        if ($this->getProfile()->getBillingAddress()->getPhone()) {
            $fields['OWNERTELNO'] = $this->getProfile()->getBillingAddress()->getPhone();
        }

        $fields['SHASIGN'] = $this->getSHASign($fields, false);

        return $fields;
    }

    /**
     * Get SHA sign for request fields
     *
     * @param array   $fields Request fields
     * @param boolean $shaOut Flag: true - SHA OUT, false - SHA IN
     *
     * @return string
     */
    protected function getSHASign($fields, $shaOut = false)
    {
        if ($shaOut) {
            $allowedParams = $this->getSHAParamsOUT();
            $shaKey = $this->getSetting('sha_out');

        } else {
            $allowedParams = $this->getSHAParamsIN();
            $shaKey = $this->getSetting('sha_in');
        }
    
        // Even though some parameters are (partially) returned in lower case by the system, for the
        // SHA-OUT calculation each parameter must be put in upper case. page 16
    
        $fields = array_change_key_case($fields, CASE_UPPER);

        ksort($fields);

        $string = '';
    
        // Basic e-Commerce 6: Security: Check prior to Payment, page 12
        // Appendix: List of parameters to be included in SHA calculations, page 18
        // 
        // Parameters that do not have a value should NOT be included in the string to hash
        //
        // All parameters must be sorted following the order in the List of parameters to be included in
        // SHA calculations (SHA-OUT)

        foreach($fields as $key => $value) {
            // Skip empty values from signature string
            if (strlen(strval($value)) > 0) {
                if (in_array($key, $allowedParams)) {
                    $string .= sprintf('%s=%s%s', $key, $value, $shaKey);
                }
            }
        }

        // Sign in request using SHA-512 hashing algorithm
        return strtoupper(hash('sha512', $string));
    }

     /**
     * Get SHA IN allwed parameters
     *
     * @return array
     */
    protected function getSHAParamsIN()
    {
        return array(
            'ACCEPTANCE',
            'ACCEPTURL',
            'ADDMATCH',
            'ADDRMATCH',
            'AIACTIONNUMBER',
            'AIAGIATA',
            'AIAIRNAME',
            'AIAIRTAX',
            'AIBOOKIND*XX*',
            'AICARRIER*XX*',
            'AICHDET',
            'AICLASS*XX*',
            'AICONJTI',
            'AIDEPTCODE',
            'AIDESTCITY*XX*',
            'AIDESTCITYL*XX*',
            'AIEXTRAPASNAME*XX*',
            'AIEYCD',
            'AIFLDATE*XX*',
            'AIFLNUM*XX*',
            'AIGLNUM',
            'AIINVOICE',
            'AIIRST',
            'AIORCITY*XX*',
            'AIORCITYL*XX*',
            'AIPASNAME',
            'AIPROJNUM',
            'AISTOPOV*XX*',
            'AITIDATE',
            'AITINUM',
            'AITINUML*XX*',
            'AITYPCH',
            'AIVATAMNT',
            'AIVATAPPL',
            'ALIAS',
            'ALIASOPERATION',
            'ALIASUSAGE',
            'ALLOWCORRECTION',
            'AMOUNT',
            'AMOUNT*XX*',
            'AMOUNTHTVA',
            'AMOUNTTVA',
            'BACKURL',
            'BATCHID',
            'BGCOLOR',
            'BLVERNUM',
            'BIC',
            'BIN',
            'BRAND',
            'BRANDVISUAL',
            'BUTTONBGCOLOR',
            'BUTTONTXTCOLOR',
            'CANCELURL',
            'CARDNO',
            'CATALOGURL',
            'CAVV_3D',
            'CAVVALGORITHM_3D',
            'CERTID',
            'CHECK_AAV',
            'CIVILITY',
            'CN',
            'COM',
            'COMPLUS',
            'CONVCCY',
            'COSTCENTER',
            'COSTCODE',
            'CREDITCODE',
            'CUID',
            'CURRENCY',
            'CVC',
            'CVCFLAG',
            'DATA',
            'DATATYPE',
            'DATEIN',
            'DATEOUT',
            'DCC_COMMPERC',
            'DCC_CONVAMOUNT',
            'DCC_CONVCCY',
            'DCC_EXCHRATE',
            'DCC_EXCHRATETS',
            'DCC_INDICATOR',
            'DCC_MARGINPERC',
            'DCC_REF',
            'DCC_SOURCE',
            'DCC_VALID',
            'DECLINEURL',
            'DEVICE',
            'DISCOUNTRATE',
            'DISPLAYMODE',
            'ECI',
            'ECI_3D',
            'ECOM_BILLTO_POSTAL_CITY',
            'ECOM_BILLTO_POSTAL_COUNTRYCODE',
            'ECOM_BILLTO_POSTAL_COUNTY',
            'ECOM_BILLTO_POSTAL_NAME_FIRST',
            'ECOM_BILLTO_POSTAL_NAME_LAST',
            'ECOM_BILLTO_POSTAL_POSTALCODE',
            'ECOM_BILLTO_POSTAL_STREET_LINE1',
            'ECOM_BILLTO_POSTAL_STREET_LINE2',
            'ECOM_BILLTO_POSTAL_STREET_NUMBER',
            'ECOM_CONSUMERID',
            'ECOM_CONSUMER_GENDER',
            'ECOM_CONSUMEROGID',
            'ECOM_CONSUMERORDERID',
            'ECOM_CONSUMERUSERALIAS',
            'ECOM_CONSUMERUSERPWD',
            'ECOM_CONSUMERUSERID',
            'ECOM_ESTIMATEDELIVERYDATE',
            'ECOM_PAYMENT_CARD_EXPDATE_MONTH',
            'ECOM_PAYMENT_CARD_EXPDATE_YEAR',
            'ECOM_PAYMENT_CARD_NAME',
            'ECOM_PAYMENT_CARD_VERIFICATION',
            'ECOM_SHIPMETHODDETAILS',
            'ECOM_SHIPMETHODSPEED',
            'ECOM_SHIPMETHODTYPE',
            'ECOM_SHIPTO_COMPANY',
            'ECOM_SHIPTO_DOB',
            'ECOM_SHIPTO_ONLINE_EMAIL',
            'ECOM_SHIPTO_POSTAL_CITY',
            'ECOM_SHIPTO_POSTAL_COUNTRYCODE',
            'ECOM_SHIPTO_POSTAL_COUNTY',
            'ECOM_SHIPTO_POSTAL_NAME_FIRST',
            'ECOM_SHIPTO_POSTAL_NAME_LAST',
            'ECOM_SHIPTO_POSTAL_NAME_PREFIX',
            'ECOM_SHIPTO_POSTAL_POSTALCODE',
            'ECOM_SHIPTO_POSTAL_STATE',
            'ECOM_SHIPTO_POSTAL_STREET_LINE1',
            'ECOM_SHIPTO_POSTAL_STREET_LINE2',
            'ECOM_SHIPTO_POSTAL_STREET_NUMBER',
            'ECOM_SHIPTO_TELECOM_FAX_NUMBER',
            'ECOM_SHIPTO_TELECOM_PHONE_NUMBER',
            'ECOM_SHIPTO_TVA',
            'ED',
            'EMAIL',
            'EXCEPTIONURL',
            'EXCLPMLIST',
            'EXECUTIONDATE*XX*',
            'FACEXCL*XX*',
            'FACTOTAL*XX*',
            'FIRSTCALL',
            'FLAG3D',
            'FONTTYPE',
            'FORCECODE1',
            'FORCECODE2',
            'FORCECODEHASH',
            'FORCEPROCESS',
            'FORCETP',
            'GENERIC_BL',
            'GIROPAY_ACCOUNT_NUMBER',
            'GIROPAY_BLZ',
            'GIROPAY_OWNER_NAME',
            'GLOBORDERID',
            'GUID',
            'HDFONTTYPE',
            'HDTBLBGCOLOR',
            'HDTBLTXTCOLOR',
            'HEIGHTFRAME',
            'HOMEURL',
            'HTTP_ACCEPT',
            'HTTP_USER_AGENT',
            'IBAN',
            'INCLUDE_BIN',
            'INCLUDE_COUNTRIES',
            'INVDATE',
            'INVDISCOUNT',
            'INVLEVEL',
            'INVORDERID',
            'ISSUERID',
            'IST_MOBILE',
            'ITEM_COUNT',
            'ITEMATTRIBUTES*XX*',
            'ITEMCATEGORY*XX*',
            'ITEMCOMMENTS*XX*',
            'ITEMDESC*XX*',
            'ITEMDISCOUNT*XX*',
            'ITEMFDMPRODUCTCATEG*XX*',
            'ITEMID*XX*',
            'ITEMNAME*XX*',
            'ITEMPRICE*XX*',
            'ITEMQUANT*XX*',
            'ITEMQUANTORIG*XX*',
            'ITEMUNITOFMEASURE*XX*',
            'ITEMVAT*XX*',
            'ITEMVATCODE*XX*',
            'ITEMWEIGHT*XX*',
            'LANGUAGE',
            'LEVEL1AUTHCPC',
            'LIDEXCL*XX*',
            'LIMITCLIENTSCRIPTUSAGE',
            'LINE_REF',
            'LINE_REF1',
            'LINE_REF2',
            'LINE_REF3',
            'LINE_REF4',
            'LINE_REF5',
            'LINE_REF6',
            'LIST_BIN',
            'LIST_COUNTRIES',
            'LOGO',
            'MANDATEID',
            'MAXITEMQUANT*XX*',
            'MERCHANTID',
            'MODE',
            'MTIME',
            'MVER',
            'NETAMOUNT',
            'OPERATION',
            'ORDERID',
            'ORDERSHIPCOST',
            'ORDERSHIPMETH',
            'ORDERSHIPTAX',
            'ORDERSHIPTAXCODE',
            'ORIG',
            'OR_INVORDERID',
            'OR_ORDERID',
            'OWNERADDRESS',
            'OWNERADDRESS2',
            'OWNERCTY',
            'OWNERTELNO',
            'OWNERTELNO2',
            'OWNERTOWN',
            'OWNERZIP',
            'PAIDAMOUNT',
            'PARAMPLUS',
            'PARAMVAR',
            'PAYID',
            'PAYMETHOD',
            'PM',
            'PMLIST',
            'PMLISTPMLISTTYPE',
            'PMLISTTYPE',
            'PMLISTTYPEPMLIST',
            'PMTYPE',
            'POPUP',
            'POST',
            'PSPID',
            'PSWD',
            'RECIPIENTACCOUNTNUMBER',
            'RECIPIENTDOB',
            'RECIPIENTLASTNAME',
            'RECIPIENTZIP',
            'REF',
            'REFER',
            'REFID',
            'REFKIND',
            'REF_CUSTOMERID',
            'REF_CUSTOMERREF',
            'REGISTRED',
            'REMOTE_ADDR',
            'REQGENFIELDS',
            'RNPOFFERT',
            'RTIMEOUT',
            'RTIMEOUTREQUESTEDTIMEOUT',
            'SCORINGCLIENT',
            'SEQUENCETYPE',
            'SETT_BATCH',
            'SID',
            'SIGNDATE',
            'STATUS_3D',
            'SUBSCRIPTION_ID',
            'SUB_AM',
            'SUB_AMOUNT',
            'SUB_COM',
            'SUB_COMMENT',
            'SUB_CUR',
            'SUB_ENDDATE',
            'SUB_ORDERID',
            'SUB_PERIOD_MOMENT',
            'SUB_PERIOD_MOMENT_M',
            'SUB_PERIOD_MOMENT_WW',
            'SUB_PERIOD_NUMBER',
            'SUB_PERIOD_NUMBER_D',
            'SUB_PERIOD_NUMBER_M',
            'SUB_PERIOD_NUMBER_WW',
            'SUB_PERIOD_UNIT',
            'SUB_STARTDATE',
            'SUB_STATUS',
            'TAAL',
            'TAXINCLUDED*XX*',
            'TBLBGCOLOR',
            'TBLTXTCOLOR',
            'TID',
            'TITLE',
            'TOTALAMOUNT',
            'TP',
            'TRACK2',
            'TXTBADDR2',
            'TXTCOLOR',
            'TXTOKEN',
            'TXTOKENTXTOKENPAYPAL',
            'TYPE_COUNTRY',
            'UCAF_AUTHENTICATION_DATA',
            'UCAF_PAYMENT_CARD_CVC2',
            'UCAF_PAYMENT_CARD_EXPDATE_MONTH',
            'UCAF_PAYMENT_CARD_EXPDATE_YEAR',
            'UCAF_PAYMENT_CARD_NUMBER',
            'USERID',
            'USERTYPE',
            'VERSION',
            'WBTU_MSISDN',
            'WBTU_ORDERID',
            'WEIGHTUNIT',
            'WIN3DS',
            'WITHROOT',
        );
    }

    /**
     * Get SHA OUT allwed parameters
     *
     * @return array
     */
    protected function getSHAParamsOUT()
    {
        return array(
            'AAVADDRESS',
            'AAVCHECK',
            'AAVMAIL',
            'AAVNAME',
            'AAVPHONE',
            'AAVZIP',
            'ACCEPTANCE',
            'ALIAS',
            'AMOUNT',
            'BIC',
            'BIN',
            'BRAND',
            'CARDNO',
            'CCCTY',
            'CN',
            'COMPLUS',
            'CREATION_STATUS',
            'CURRENCY',
            'CVCCHECK',
            'DCC_COMMPERCENTAGE',
            'DCC_CONVAMOUNT',
            'DCC_CONVCCY',
            'DCC_EXCHRATE',
            'DCC_EXCHRATESOURCE',
            'DCC_EXCHRATETS',
            'DCC_INDICATOR',
            'DCC_MARGINPERCENTAGE',
            'DCC_VALIDHOURS',
            'DIGESTCARDNO',
            'ECI',
            'ED',
            'ENCCARDNO',
            'FXAMOUNT',
            'FXCURRENCY',
            'IBAN',
            'IP',
            'IPCTY',
            'NBREMAILUSAGE',
            'NBRIPUSAGE',
            'NBRIPUSAGE_ALLTX',
            'NBRUSAGE',
            'NCERROR',
            'NCERRORCARDNO',
            'NCERRORCN',
            'NCERRORCVC',
            'NCERRORED',
            'ORDERID',
            'PAYID',
            'PM',
            'SCO_CATEGORY',
            'SCORING',
            'STATUS',
            'SUBBRAND',
            'SUBSCRIPTION_ID',
            'TRXDATE',
            'VC',
        );
    }

    /**
     * Get list of ePDQ response statuses
     *
     * @return array
     */
    protected function getEPDQStatuses()
    {
        return array(
            0 => 'Incomplete or invalid',
            1 => 'Cancelled by client',
            2 => 'Authorisation refused',
            4 => 'Order stored',
            41 => 'Waiting client payment',
            5 => 'Authorised',
            51 => 'Authorisation waiting',
            52 => 'Authorisation not known',
            59 => 'Author. to get manually',
            6 => 'Authorised and canceled',
            61 => 'Author. deletion waiting',
            62 => 'Author. deletion uncertain',
            63 => 'Author. deletion refused',
            7 => 'Payment deleted',
            71 => 'Payment deletion pending',
            72 => 'Payment deletion uncertain',
            73 => 'Payment deletion refused',
            74 => 'Payment deleted (not accepted)',
            75 => 'Deletion processed by merchant',
            8 => 'Refund',
            81 => 'Refund pending',
            82 => 'Refund uncertain',
            83 => 'Refund refused',
            84 => 'Payment declined by the acquirer (will be debited)',
            85 => 'Refund processed by merchant',
            9 => 'Payment requested',
            91 => 'Payment processing',
            92 => 'Payment uncertain',
            93 => 'Payment refused',
            94 => 'Refund declined by the acquirer',
            95 => 'Payment processed by merchant',
            97 => 'Being processed (intermediate technical status)',
            98 => 'Being processed (intermediate technical status)',
            99 => 'Being processed (intermediate technical status)'
        );
    }

    /**
     * Get list of status code for successful transactions
     *
     * @return array
     */
    protected function getSuccessfulStatuses()
    {
        return array(5, 51, 9, 91);
    }

    /**
     * Define saved into transaction data schema
     *
     * @return array
     */
    protected function defineSavedData()
    {
        $data = parent::defineSavedData();

        $data['orderID'] = 'Order reference';
        $data['amount'] = 'Order amount';
        $data['currency'] = 'Currency of the order';
        $data['TRXDATE'] = 'Transaction date';
        $data['PM'] = 'Payment method';
        $data['BRAND'] = 'Payment method info';
        $data['ACCEPTANCE'] = 'Acceptance code returned by acquirer';
        $data['STATUS'] = 'Transaction status code';
        $data['PAYID'] = 'Payment reference in ePDQ system';
        $data['NCERROR'] = 'Error code';

        return $data;
    }

    /**
     * Return true if response from ePDQ contains correct signature
     *
     * @param array $data Response data
     *
     * @return boolean
     */
    protected function isResponseTrusted($data)
    {
        $result = false;

        if (!empty($data['SHASIGN'])) {
    
            $requestSignature = strtoupper($data['SHASIGN']);
            unset($data['SHASIGN']);
            $checkedSignature = $this->getSHASign($data, true);

            $result = ($checkedSignature == $requestSignature);
        }

        return $result;
    }
}
