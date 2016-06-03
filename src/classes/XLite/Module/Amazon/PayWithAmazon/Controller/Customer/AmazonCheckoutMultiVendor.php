<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\Controller\Customer;

/**
 * Amazon checkout controller
 * @Decorator\Depend ("XC\MultiVendor")
 */
class AmazonCheckoutMultiVendor extends \XLite\Module\Amazon\PayWithAmazon\Controller\Customer\AmazonCheckout implements \XLite\Base\IDecorator
{
    /**
     * Process cart profile
     *
     * @return boolean
     */
    protected function processCartProfile()
    {
        $result = parent::processCartProfile();

        $cart = $this->getCart();
        if ($cart->isParent()) {
            foreach ($cart->getChildren() as $child) {
                $child->setOrigProfile($cart->getOrigProfile());

                $profile = $cart->getProfile()->cloneEntity();
                $child->setProfile($profile);
                $profile->setOrder($child);
            }
        }

        \XLite\Core\Database::getEM()->flush();

        return $result;
    }
}
