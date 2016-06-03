<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\View\Form\Product;

/**
 * Place order form
 */
class AddToCart extends \XLite\View\Form\Product\AddToCart implements \XLite\Base\IDecorator
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
            && \XLite\Module\CDev\Paypal\Main::getMerchantId()
        ) {
            $list['data-paypal-id'] = \XLite\Module\CDev\Paypal\Main::getMerchantId();
        }

        return $list;
    }

    /**
     * getFormDefaultParams
     *
     * @return array
     */
    protected function getFormDefaultParams()
    {
        $list = parent::getFormDefaultParams();

        if (\XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled()) {
            $list['expressCheckout'] = false;

            if (\XLite\Module\CDev\Paypal\Main::isInContextCheckoutAvailable()) {
                $list['inContext'] = true;
                $list['cancelUrl'] = $this->isAjax()
                    ? $this->getReferrerURL()
                    : \XLite\Core\URLManager::getSelfURI();
            }
        }

        return $list;
    }
}
