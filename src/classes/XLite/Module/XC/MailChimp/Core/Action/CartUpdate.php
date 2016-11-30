<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Action;


use XLite\Module\XC\MailChimp\Core\MailChimp;

class CartUpdate implements IMailChimpAction
{
    /**
     * @var \XLite\Model\Cart
     */
    private $cart;

    /**
     * @inheritDoc
     */
    public function __construct(\XLite\Model\Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     *
     */
    public function execute()
    {
        try {
            MailChimp::getInstance()->createOrUpdateCart($this->cart);
        } catch (\Exception $e) {
            \XLite\Logger::getInstance()->log($e->getMessage());
        }
    }
}