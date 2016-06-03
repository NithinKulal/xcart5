<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

/**
 * Abstract order status
 *
 * @MappedSuperclass 
 */
abstract class AStatus extends \XLite\Model\Base\I18n
{
    /**
     * ID
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Status code
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=4, unique=true, nullable=true)
     */
    protected $code;

    /**
     * Position
     *
     * @var integer
     *
     * @Column (type="integer")
     */
    protected $position = 0;

    /**
     * Status is allowed to set manually
     *
     * @return boolean
     */
    abstract public function isAllowedToSetManually();

    /**
     * Return status handlers list
     *
     * @return array
     */
    public static function getStatusHandlers()
    {
        return static::$statusHandlers;
    }
}
