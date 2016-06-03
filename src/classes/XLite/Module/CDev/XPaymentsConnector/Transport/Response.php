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

namespace XLite\Module\CDev\XPaymentsConnector\Transport;

/**
 * X-Payments reponse
 */
class Response extends \XLite\Base
{
    /**
     * Response statuses
     */
    const STATUS_SUCCESS = '1';
    const STATUS_FAILED = '0';

    /**
     * Response status
     */
    protected $status = self::STATUS_FAILED;

    /**
     * Response. Encrypted string -> XML -> Array
     */
    protected $response = '';

    /**
     * Error message
     */
    protected $error = '';

    /**
     * Set successfull status
     * 
     * @return void
     */
    public function setSuccess()
    {
        $this->status = self::STATUS_SUCCESS;
    }

    /** 
     * Set failed status
     * 
     * @return void
     */
    public function setFailed()
    {
        $this->status = self::STATUS_FAILED;
    }

    /**
     * Set status
     *
     * @param int $status Status
     *
     * @return void
     */ 
    public function setStatus($status)
    {
        if ($status == self::STATUS_SUCCESS) {
            $this->status = self::STATUS_SUCCESS;
        } else {
            $this->status = self::STATUS_FAILED;
        }
    }

    /**
     * Is reponse successfull (no errors)
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->status == self::STATUS_SUCCESS;
    }

    /**
     * Is response filed (any error occured)
     *
     * @return bool
     */
    public function isFailed()
    {
        return $this->status == self::STATUS_FAILED;
    }

    /**
     * Set error and mark failed
     *
     * @param string $error Error messsage
     *
     * @return void
     */
    public function setError($error)
    {
        $this->error = $error;
        $this->status == self::STATUS_FAILED;
    }

    /**
     * Set response
     *
     * @param mixed $response Response
     *
     * @return void
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Cleanup the response, 
     *  - erase response and error data
     *  - set default (failed) status
     *
     * @return void
     */
    public function cleanup()
    {
        $this->response = $this->error = '';
        $this->status == self::STATUS_FAILED;
    }

    /**
     * Fill in the properties
     *
     * @param int $status Status
     * @param mixed $response Response
     * @param string $error Error message
     *
     * @return void
     */
    public function fill($status = null, $response = null, $error = null)
    {
        if (true === $status) {
            $this->status = self::STATUS_SUCCESS;
        }

        $this->status = $status;

        if (!is_null($response)) {
            $this->response = $response;
        }

        if (!is_null($error)) {
            $this->error = $error;
        }
    }

    /**
     * Get response
     * 
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get error
     * 
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
