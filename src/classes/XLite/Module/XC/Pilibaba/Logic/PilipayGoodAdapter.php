<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Pilibaba\Logic;

/**
 * Pilibaba payment processor
 */
class PilipayGoodAdapter
{
    /**
     * @var \XLite\Model\OrderItem
     */
    protected $item;

    /**
     * Constructor
     *
     * @param \XLite\Model\OrderItem $item
     *
     * @return void
     */
    public function __construct(\XLite\Model\OrderItem $item)
    {
        \XLite\Module\XC\Pilibaba\Main::includeLibrary();

        $this->item = $item;
    }

    /**
     * Process
     *
     * @return PilipayGood
     */
    protected function process()
    {
        $product = $this->item->getProduct();

        $good = new \PilipayGood();
        $good->name         = $product->getName();
        $good->pictureUrl   = $product->hasImage()
            ? $product->getImageURL()
            : '';
        $good->price        = $product->getClearPrice();
        $good->productUrl   = $product->getFrontURL();
        $good->productId    = $product->getProductId();
        $good->quantity     = $this->item->getAmount();
        $good->weight       = $product->getWeight() * $good->quantity;
        $good->weightUnit   = \XLite\Core\Config::getInstance()->Units->weight_unit;

        // Should be optional, but SDK throws exceptions if doesn't set
        $good->height       = 0;
        $good->width        = 0;
        $good->length       = 0;
        $good->attr         = ''; // TODO prepare attributes values
        $good->category     = $product->getCategory()->getName();

        return $good;
    }

    /**
     * Get mapped result
     *
     * @return PilipayGood
     */
    public function getResult()
    {
        if (!$this->item) {
            throw new \Exception("You should set a OrderItem for this adapter", 1);
        }

        return $this->process();
    }
}
