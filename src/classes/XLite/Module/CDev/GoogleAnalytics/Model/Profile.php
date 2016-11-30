<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Model;


class Profile extends \XLite\Model\Profile implements \XLite\Base\IDecorator
{
    /**
     * Google analytics cid
     *
     * @var string
     * @Column (type="string")
     */
    protected $gaClientId = '';

    /**
     * @return string
     */
    public function getGaClientId()
    {
        return $this->gaClientId;
    }

    /**
     * @param string $gaClientId
     */
    public function setGaClientId($gaClientId)
    {
        $this->gaClientId = $gaClientId;
    }
}