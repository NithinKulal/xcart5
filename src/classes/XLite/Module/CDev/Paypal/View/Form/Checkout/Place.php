<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Form\Checkout;

/**
 * Place order form
 */
class Place extends \XLite\View\Form\Checkout\Place implements \XLite\Base\IDecorator
{
    /**
     * Return form attributes
     *
     * @return array
     */
    protected function getFormAttributes()
    {
        $list = parent::getFormAttributes();

        if (
            \XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled()
            && \XLite\Module\CDev\Paypal\Main::isInContextCheckoutAvailable()
        ) {
            $list['data-paypal-id'] = \XLite\Module\CDev\Paypal\Main::getMerchantId();
        }

        return $list;
    }
}
