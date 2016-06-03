<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Checkout;

/**
 * Shipping method selection
 */
class ShippingMethod extends \XLite\View\Form\Checkout\ACheckout
{
    /**
     * getDefaultAction
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'shipping';
    }

    /**
     * Check if view should reload ajax-ly after page load (in case of online shippings)
     *
     * @return boolean
     */
    public function shouldDeferLoad()
    {
         return \XLite\Model\Shipping::getInstance()->hasOnlineProcessors();
    }

    /**
     * Return form attributes
     *
     * @return array
     */
    protected function getFormAttributes()
    {
        $list = parent::getFormAttributes();

        $deferred = $this->shouldDeferLoad();

        $list['data-deferred'] = ($deferred) ? 'true' : 'false' ;

        return $list;
    }
}
