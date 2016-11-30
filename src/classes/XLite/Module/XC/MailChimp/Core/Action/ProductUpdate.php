<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core\Action;

use XLite\Module\XC\MailChimp\Core\MailChimpECommerce;
use XLite\Module\XC\MailChimp\Main;

class ProductUpdate implements IMailChimpAction
{
    /**
     * @var \XLite\Model\Product
     */
    private $product;

    /**
     * @inheritDoc
     */
    public function __construct(\XLite\Model\Product $product)
    {
        $this->product = $product;
    }

    /**
     *
     */
    public function execute()
    {
        $ecCore = MailChimpECommerce::getInstance();

        foreach (Main::getMainStores() as $store) {
            $updateResult = $ecCore->updateProduct($store->getId(), $this->product);
            if ($updateResult === null) {
                $ecCore->createProduct($store->getId(), $this->product);
            }
        }
    }
}