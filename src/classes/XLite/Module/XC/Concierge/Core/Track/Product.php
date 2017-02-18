<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core\Track;

use XLite\Model\Product as ProductModel;
use XLite\Module\XC\Concierge\Core\ATrack;

class Product extends ATrack
{
    /**
     * @var ProductModel
     */
    protected $product;

    /**
     * PaymentMethod constructor.
     *
     * @param string       $event
     * @param ProductModel $product
     */
    public function __construct($event, $product)
    {
        $this->event   = $event;
        $this->product = $product;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        $product = $this->getProduct();

        return [
            'Product Name' => $product->getName(),
            'Product Id'   => $product->getProductId(),
        ];
    }

    /**
     * @return ProductModel
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param ProductModel $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }
}
