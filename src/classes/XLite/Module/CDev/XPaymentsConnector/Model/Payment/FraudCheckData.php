<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * X-Cart
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the software license agreement
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.x-cart.com/license-agreement.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to licensing@x-cart.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not modify this file if you wish to upgrade X-Cart to newer versions
 * in the future. If you wish to customize X-Cart for your needs please
 * refer to http://www.x-cart.com/ for more information.
 *
 * @category  X-Cart 5
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 */

namespace XLite\Module\CDev\XPaymentsConnector\Model\Payment;

/**
 * X-Payments payment transaction data
 *
 * @Entity
 * @Table  (name="xpc_payment_fraud_check_data")
 */

class FraudCheckData extends \XLite\Model\AEntity
{
    /**
     * Result of the fraud check
     */
    const RESULT_UNKNOWN  = 0;
    const RESULT_ACCEPTED = 1;
    const RESULT_REVIEW   = 2;
    const RESULT_FAIL     = 3;

    /**
     * Codes defining anti fraud service/system
     */
    const CODE_KOUNT     = 'kount';
    const CODE_NOFRAUD   = 'nofraud';
    const CODE_GATEWAY   = 'gateway';
    const CODE_XPAYMENTS = 'xpayments';

    /**
     * Kount messages
     */
    private $kountMessages = array(
       self::RESULT_FAIL     => 'High fraud risk detected',
       self::RESULT_ACCEPTED => 'Antifraud check passed',
       self::RESULT_REVIEW   => 'Manual Review required',
    );

    /**
     * NoFraud messages
     */
    private $noFraudMessages = array(
       self::RESULT_FAIL     => 'High fraud risk detected',
       self::RESULT_ACCEPTED => 'Antifraud check passed',
       self::RESULT_REVIEW   => 'Being Reviewed by NoFraud',
    );

    /**
     * CSS class for score on the order details page
     */
    private $scoreClass = array(
       self::RESULT_FAIL     => 'danger',
       self::RESULT_ACCEPTED => 'success',
       self::RESULT_REVIEW   => 'warning',
    );

    /**
     * Unique ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Code of the fraud check service/system
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $code = '';

    /**
     * Name of the fraud check service/system
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $service = '';

    /**
     * Result of the fraud check
     *
     * @var integer 
     *
     * @Column (type="integer")
     */
    protected $result = self::RESULT_UNKNOWN;

    /**
     * Status of the fraud check (as it's returned by the service/system)
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $status = '';

    /**
     * Fraud score
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $score = 0;

    /**
     * Message
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $message = '';

    /**
     * Service transaction ID
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $serviceTransactionId = '';

    /**
     * URL (e.g. to the transaction)
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $url = '';

    /**
     * List of errors
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $errors = '';

    /**
     * List of warnings
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $warnings = '';

    /**
     * List of triggered rules
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $rules = '';

    /**
     * Some other data
     *
     * @var string
     *
     * @Column (type="string")
     */
    protected $data = '';

    /**
     * One-to-one relation with payment transaction
     *
     * @var \XLite\Model\Payment\Transaction
     *
     * @ManyToOne (targetEntity="XLite\Model\Payment\Transaction", inversedBy="xpc_payment_fraud_check_data", fetch="LAZY", cascade={"merge","detach","persist"})
     * @JoinColumn (name="transaction_id", referencedColumnName="transaction_id")
     */
    protected $transaction;

    /**
     * Check if transaction is potentially fraudulent
     *
     * @return bool
     */
    public function isPending()
    {
        return self::RESULT_REVIEW == $this->getResult();
    }

    /**
     * Get message to be displayed on the order details
     *
     * @return string
     */
    public function getDisplayMessage()
    {
        $message = $this->getMessage();

        if (
            self::CODE_KOUNT == $this->getCode()
            && isset($this->kountMessages[$this->getResult()])
        ) {

            $message = $this->kountMessages[$this->getResult()];

        } elseif (
            self::CODE_NOFRAUD == $this->getCode()
            && isset($this->noFraudMessages[$this->getResult()])
        ) {
            
            $message = $this->noFraudMessages[$this->getResult()];
        }

        return $message;
    }

    /**
     * Get CSS class for score
     *
     * @return string
     */
    public function getScoreClass()
    {
        $class = '';

        if (isset($this->scoreClass[$this->getResult()])) {
            $class = $this->scoreClass[$this->getResult()];
        }

        return $class;
    }

    /**
     * Return errors as an array
     *
     * return array()
     */
    public function getErrorsList()
    {
        return explode("\n", $this->getErrors());
    }

    /**
     * Return warnings as an array
     *
     * return array()
     */
    public function getWarningsList()
    {
        return explode("\n", $this->getWarnings());
    }

    /**
     * Return triggered rules as an array
     *
     * return array()
     */
    public function getRulesList()
    {
        $rules = explode("\n", $this->getRules());

        if (
            !empty($rules)
            && self::CODE_KOUNT == $this->getCode()
        ) {
            foreach ($rules as $key => $rule) {

                // Remove rule ID fro the beginning
                $rules[$key] = preg_replace('/^\d+ /', '', $rule);
            }
        }

        return $rules;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return FraudCheckData
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set service
     *
     * @param string $service
     * @return FraudCheckData
     */
    public function setService($service)
    {
        $this->service = $service;
        return $this;
    }

    /**
     * Get service
     *
     * @return string 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set result
     *
     * @param integer $result
     * @return FraudCheckData
     */
    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     * Get result
     *
     * @return integer 
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return FraudCheckData
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return FraudCheckData
     */
    public function setScore($score)
    {
        $this->score = $score;
        return $this;
    }

    /**
     * Get score
     *
     * @return integer 
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return FraudCheckData
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set serviceTransactionId
     *
     * @param string $serviceTransactionId
     * @return FraudCheckData
     */
    public function setServiceTransactionId($serviceTransactionId)
    {
        $this->serviceTransactionId = $serviceTransactionId;
        return $this;
    }

    /**
     * Get serviceTransactionId
     *
     * @return string 
     */
    public function getServiceTransactionId()
    {
        return $this->serviceTransactionId;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return FraudCheckData
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set errors
     *
     * @param string $errors
     * @return FraudCheckData
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Get errors
     *
     * @return string 
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set warnings
     *
     * @param string $warnings
     * @return FraudCheckData
     */
    public function setWarnings($warnings)
    {
        $this->warnings = $warnings;
        return $this;
    }

    /**
     * Get warnings
     *
     * @return string 
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * Set rules
     *
     * @param string $rules
     * @return FraudCheckData
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Get rules
     *
     * @return string 
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return FraudCheckData
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set transaction
     *
     * @param \XLite\Model\Payment\Transaction $transaction
     * @return FraudCheckData
     */
    public function setTransaction(\XLite\Model\Payment\Transaction $transaction = null)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * Get transaction
     *
     * @return \XLite\Model\Payment\Transaction 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
