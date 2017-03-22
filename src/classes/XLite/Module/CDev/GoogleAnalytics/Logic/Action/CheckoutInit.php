<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic\Action;

use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\OrderItemDataMapper;

class CheckoutInit implements IAction
{
    /**
     * @return bool
     */
    public function isApplicable()
    {
        return \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
            && \XLite::getController() instanceof \XLite\Controller\Customer\Checkout
            && $this->getCart();
    }

    /**
     * @return array
     */
    public function getActionData()
    {
        $result = [
            'ga-type'   => 'checkout',
            'ga-action' => 'pageview',
            'data'      => $this->getCheckoutActionData($this->getCart())
        ];

        return $result;
    }

    /**
     * @param \XLite\Model\Cart $cart
     *
     * @return array
     */
    protected function getCheckoutActionData(\XLite\Model\Cart $cart)
    {
        $productsData = [];

        \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);

        foreach ($cart->getItems() as $item) {
            $productsData[] = OrderItemDataMapper::getData(
                $item,
                $item->getObject()->getCategory() ? $item->getObject()->getCategory()->getName() : ''
            );
        }
        \XLite\Core\Translation::setTmpTranslationCode(null);

        $actionData = [];

        return [
            'products'      => $productsData,
            'actionData'    => (object) $actionData,
        ];
    }

    /**
     * @return \XLite\Model\Cart
     */
    protected function getCart()
    {
        return \XLite::getController()->getCart();
    }
}
