<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

/**
 * Shipping status
 *
 * @Entity
 * @Table  (name="order_shipping_statuses",
 *      indexes={
 *          @Index (name="code", columns={"code"})
 *      }
 * )
 */
class Shipping extends \XLite\Model\Order\Status\AStatus
{
    /**
     * Statuses
     */
    const STATUS_NEW              = 'N';
    const STATUS_PROCESSING       = 'P';
    const STATUS_SHIPPED          = 'S';
    const STATUS_DELIVERED        = 'D';
    const STATUS_WILL_NOT_DELIVER = 'WND';
    const STATUS_RETURNED         = 'R';

    /**
     * List of change status handlers;
     * top index - old status, second index - new one
     * (<old_status> ----> <new_status>: $statusHandlers[$old][$new])
     *
     * @var array
     */
    protected static $statusHandlers = array(
        self::STATUS_NEW => array(
            self::STATUS_SHIPPED => array('ship'),
        ),

        self::STATUS_PROCESSING => array(
            self::STATUS_SHIPPED => array('ship'),
        ),

        self::STATUS_DELIVERED => array(
            self::STATUS_SHIPPED => array('ship'),
        ),

        self::STATUS_WILL_NOT_DELIVER => array(
            self::STATUS_SHIPPED => array('ship'),
        ),

        self::STATUS_RETURNED => array(
            self::STATUS_SHIPPED => array('ship'),
        ),
    );

    /**
     * Status is allowed to set manually
     *
     * @return boolean
     */
    public function isAllowedToSetManually()
    {
        return true;
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
     * @return Shipping
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
     * @return Shipping
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
