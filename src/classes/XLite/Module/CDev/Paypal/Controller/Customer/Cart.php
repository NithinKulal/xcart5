<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

/**
 * Checkout controller
 */
class Cart extends \XLite\Controller\Customer\Cart implements \XLite\Base\IDecorator
{
    /**
     * Product status in cart
     *
     * @var boolean
     */
    protected $isAddedSuccessfully = false;

    /**
     * Process 'Add item' success
     *
     * @return void
     */
    protected function processAddItemSuccess()
    {
        // todo: rewrite add2cartPopup top message block
        // parent::processAddItemSuccess();

        if (\XLite\Module\CDev\Paypal\Main::isExpressCheckoutEnabled($this->getCart())
            && !\XLite\Core\Request::getInstance()->expressCheckout
        ) {
            \XLite\Core\TopMessage::addInfo(
                new \XLite\Module\CDev\Paypal\View\Button\TopMessage\ExpressCheckout()
            );
        } else {
            parent::processAddItemSuccess();
        }
    }

    /**
     * URL to return after product is added
     *
     * @return string
     */
    protected function setURLToReturn()
    {
        if (\XLite\Core\Request::getInstance()->expressCheckout) {
            $params = array(
                'cancelUrl' => \XLite\Core\Request::getInstance()->cancelUrl,
            );

            if (\XLite\Core\Request::getInstance()->inContext) {
                $params['inContext'] = true;
            }

            $url = \XLite::getInstance()->getShopURL(
                $this->buildURL('checkout', 'start_express_checkout', $params),
                \XLite\Core\Config::getInstance()->Security->customer_security
            );

            $this->setReturnURL($url);

        } else {
            parent::setURLToReturn();
        }
    }
}
