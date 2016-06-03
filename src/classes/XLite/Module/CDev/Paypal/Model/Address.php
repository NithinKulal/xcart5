<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Model;

/**
 * Order model
 */
class Address extends \XLite\Model\Address implements \XLite\Base\IDecorator
{
    /**
     * Get required fields by address type
     *
     * @param string $atype Address type code
     *
     * @return array
     */
    public function getRequiredFieldsByType($atype)
    {
        $list = parent::getRequiredFieldsByType($atype);
        if ('express_checkout_return' === \XLite::getController()->getAction()) {
            $list = array_diff($list, array('phone'));
        }

        return $list;
    }
}