<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Add2CartPopup\Controller\Customer;

/**
 * Cart page controller extension
 */
class Cart extends \XLite\Controller\Customer\Cart implements \XLite\Base\IDecorator
{
    /**
     * Add order item to cart.
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return boolean
     */
    protected function addItem($item)
    {
        $isAdded = parent::addItem($item);

        if ($isAdded) {

            // Recalculate cart
            $this->getCart()->calculate();

            // Save last item data in the session
            $addedItem = $this->getCart()->getItemByItem($item);
            \XLite\Core\Session::getInstance()->lastAddedCartItemId = $addedItem->getItemId();
            \XLite\Core\Session::getInstance()->lastAddedCartItemKey = $addedItem->getKey();

            $target = \XLite\Core\Request::getInstance()->target;
            \XLite\Core\Request::getInstance()->target = 'add2_cart_popup';

            $widget = new \XLite\Module\XC\Add2CartPopup\View\Add2Cart(
                array(
                    \XLite\Module\XC\Add2CartPopup\View\Add2Cart::PARAM_DISPLAY_CACHED => false
                )
            );
            $widget->init();

            $content = $widget->getContent();
            \XLite\Core\Session::getInstance()->add2CartPopupContent = $content;

            \XLite\Core\Request::getInstance()->target = $target;
        }

        return $isAdded;
    }

    /**
     * Do not add successful top message
     *
     * @return void
     */
    protected function processAddItemSuccess()
    {
    }

    /**
     * Disable redirect to cart after 'Add-to-cart' action
     *
     * @return void
     */
    protected function setURLToReturn()
    {
        \XLite\Core\Config::getInstance()->General->redirect_to_cart = false;

        parent::setURLToReturn();
    }
}
