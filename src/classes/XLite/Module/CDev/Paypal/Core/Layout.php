<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Core;

/**
 * Layout manager
 */
abstract class Layout extends \XLite\Core\Layout implements \XLite\Base\IDecorator
{
    /**
     * Defines the LESS files to be part of the main LESS queue
     *
     * @param string $interface Interface to use: admin or customer values
     *
     * @return array
     */
    public function getLESSResources($interface)
    {
        $result = parent::getLESSResources($interface);

        if (\XLite::CUSTOMER_INTERFACE === $interface) {
            $result[] = 'modules/CDev/Paypal/style.less';
        }

        return $result;
    }
}
