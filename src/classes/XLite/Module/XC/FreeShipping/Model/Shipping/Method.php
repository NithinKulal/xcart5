<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\FreeShipping\Model\Shipping;

/**
 * Shipping method model
 */
class Method extends \XLite\Model\Shipping\Method implements \XLite\Base\IDecorator
{
    /**
     * Special code values for free ship and fixed fee methods
     */
    const METHOD_TYPE_FREE_SHIP = 'FREESHIP';
    const METHOD_TYPE_FIXED_FEE = 'FIXEDFEE';

    /**
     * Whether the method is free or not
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $free = false;

    /**
     * Set free
     *
     * @param boolean $free
     * @return Method
     */
    public function setFree($free)
    {
        $this->free = $free;
        return $this;
    }

    /**
     * Get free
     *
     * @return boolean 
     */
    public function getFree()
    {
        return $this->free;
    }
}
