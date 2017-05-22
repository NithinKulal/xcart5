<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\CustomerAttachments\View\Form\Product;


class AddToCart extends \XLite\View\Form\Product\AddToCart implements \XLite\Base\IDecorator
{
    /**
     * Ability to add the 'enctype="multipart/form-data"' form attribute
     *
     * @return boolean
     */
    protected function isMultipart()
    {
        return true;
    }

    /**
     * JavaScript: this value will be returned on form submit
     * NOTE - this function designed for AJAX easy switch on/off
     *
     * @return string
     */
    protected function getOnSubmitResult()
    {
        $productId = \XLite\Core\Request::getInstance()->product_id;
        $product = \XLite\Core\Database::getRepo('\XLite\Model\Product')->find($productId);

        return $product && $product->getIsCustomerAttachmentsAvailable() ? 'true' : 'false';
    }
}
