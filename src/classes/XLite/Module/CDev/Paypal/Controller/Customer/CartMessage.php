<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Paypal\Controller\Customer;

/**
 * Checkout controller
 *
 * @Decorator\Depend("!XC\Add2CartPopup")
 */
class CartMessage extends \XLite\Controller\Customer\Cart implements \XLite\Base\IDecorator
{
    /**
     * Process 'Add item' success
     *
     * @return void
     */
    protected function processAddItemSuccess()
    {
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
}
