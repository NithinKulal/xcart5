<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

/**
 * Payment status
 *
 * @Entity
 * @Table  (name="order_payment_statuses",
 *      indexes={
 *          @Index (name="code", columns={"code"})
 *      }
 * )
 */
class Payment extends \XLite\Model\Order\Status\AStatus
{
    /**
     * Statuses
     */
    const STATUS_AUTHORIZED     = 'A';
    const STATUS_PART_PAID      = 'PP';
    const STATUS_PAID           = 'P';
    const STATUS_DECLINED       = 'D';
    const STATUS_CANCELED       = 'C';
    const STATUS_QUEUED         = 'Q';
    const STATUS_REFUNDED       = 'R';

    /**
     * List of change status handlers;
     * top index - old status, second index - new one
     * (<old_status> ----> <new_status>: $statusHandlers[$old][$new])
     *
     * @var array
     */
    protected static $statusHandlers = array(
        self::STATUS_QUEUED => array(
            self::STATUS_AUTHORIZED => array('authorize'),
            self::STATUS_PAID       => array('process'),
            self::STATUS_DECLINED   => array('decline', 'uncheckout', 'fail'),
            self::STATUS_CANCELED   => array('decline', 'uncheckout', 'cancel'),
        ),

        self::STATUS_AUTHORIZED => array(
            self::STATUS_PAID       => array('process'),
            self::STATUS_DECLINED   => array('decline', 'uncheckout', 'fail'),
            self::STATUS_CANCELED   => array('decline', 'uncheckout', 'cancel'),
        ),

        self::STATUS_PART_PAID => array(
            self::STATUS_PAID       => array('process'),
            self::STATUS_DECLINED   => array('decline', 'uncheckout', 'fail'),
            self::STATUS_CANCELED   => array('decline', 'uncheckout', 'fail'),
        ),

        self::STATUS_PAID => array(
            self::STATUS_DECLINED   => array('decline', 'uncheckout', 'fail'),
            self::STATUS_CANCELED   => array('decline', 'uncheckout', 'cancel'),
        ),

        self::STATUS_DECLINED => array(
            self::STATUS_AUTHORIZED => array('checkout', 'queue', 'authorize'),
            self::STATUS_PART_PAID  => array('checkout', 'queue'),
            self::STATUS_PAID       => array('checkout', 'queue', 'process'),
            self::STATUS_QUEUED     => array('checkout', 'queue'),
        ),

        self::STATUS_CANCELED => array(
            self::STATUS_AUTHORIZED => array('checkout', 'queue', 'authorize'),
            self::STATUS_PART_PAID  => array('checkout', 'queue'),
            self::STATUS_PAID       => array('checkout', 'queue', 'process'),
            self::STATUS_QUEUED     => array('checkout', 'queue'),
        ),
    );

    /**
     * Disallowed to set manually statuses
     *
     * @var array
     */
    protected static $disallowedToSetManuallyStatuses = array(
        self::STATUS_AUTHORIZED,
    );

    /**
     * Not compatible with Shipping status
     *
     * @var array
     */
    protected static $notCompatibleWithShippingStatus  = array(
        self::STATUS_DECLINED,
        self::STATUS_CANCELED,
    );

    /**
     * Get open order statuses
     *
     * @return array
     */
    public static function getOpenStatuses()
    {
        return array(
            static::STATUS_AUTHORIZED,
            static::STATUS_PART_PAID,
            static::STATUS_PAID,
            static::STATUS_QUEUED,
            static::STATUS_REFUNDED,
        );
    }

    /**
     * Get paid statuses
     *
     * @return array
     */
    public static function getPaidStatuses()
    {
        return array(
            static::STATUS_AUTHORIZED,
            static::STATUS_PART_PAID,
            static::STATUS_PAID,
        );
    }

    /**
     * Payment status is compatible with shipping status
     *
     * @return boolean
     */
    public function isCompatibleWithShippingStatus()
    {
        return !in_array(
            $this->getCode(),
            static::$notCompatibleWithShippingStatus
        );
    }

    /**
     * Status is allowed to set manually
     *
     * @return boolean
     */
    public function isAllowedToSetManually()
    {
        return !in_array(
            $this->getCode(),
            static::$disallowedToSetManuallyStatuses
        );
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
     * @return Payment
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
     * Set position
     *
     * @param integer $position
     * @return Payment
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }
}
