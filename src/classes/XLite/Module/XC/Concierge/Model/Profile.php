<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Model;

/**
 * Module
 */
abstract class Profile extends \XLite\Model\Profile implements \XLite\Base\IDecorator
{
    /**
     * @var string
     *
     * @Column (type="string", length=128, nullable=true)
     */
    protected $conciergeUserId;

    /**
     * @return string
     */
    public function getConciergeUserId()
    {
        return $this->conciergeUserId;
    }

    /**
     * @param string $conciergeUserId
     */
    public function setConciergeUserId($conciergeUserId)
    {
        $this->conciergeUserId = $conciergeUserId;
    }
}
