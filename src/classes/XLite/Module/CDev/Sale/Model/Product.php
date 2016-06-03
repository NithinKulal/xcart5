<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Model;

/**
 * Product
 *
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * The "Discount type" field is equal to this constant if it is "Sale price"
     */
    const SALE_DISCOUNT_TYPE_PRICE   = 'sale_price';

    /**
     * The "Discount type" field is equal to this constant if it is "Percent off"
     */
    const SALE_DISCOUNT_TYPE_PERCENT = 'sale_percent';

    /**
     * Flag, if the product participates in the sale
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $participateSale = false;

    /**
     * self::SALE_DISCOUNT_TYPE_PRICE   if "sale value" is considered as "Sale price",
     * self::SALE_DISCOUNT_TYPE_PERCENT if "sale value" is considered as "Percent Off".
     *
     * @var string
     *
     * @Column (type="string", length=32, nullable=false)
     */
    protected $discountType = self::SALE_DISCOUNT_TYPE_PRICE;

    /**
     * "Sale value"
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $salePriceValue = 0;

    /**
     * Get discount type
     *
     * @return string
     */
    public function getDiscountType()
    {
        return $this->discountType ?: self::SALE_DISCOUNT_TYPE_PRICE;
    }

    /**
     * Set it to display price with discounts to use in quick data
     *
     * @return decimal
     */
    public function getQuickDataPrice()
    {
        $price = $this->getNetPrice();

        foreach ($this->prepareAttributeValues() as $av) {

            if (is_object($av)) {
                $price += $av->getAbsoluteValue('price');
            }
        }

        return $price;
    }

    /**
     * Return old net product price (before sale)
     *
     * @return float
     */
    public function getNetPriceBeforeSale()
    {
        return \XLite\Module\CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getClearPrice', array('taxable'), 'net');
    }

    /**
     * Return old display product price (before sale)
     *
     * @return float
     */
    public function getDisplayPriceBeforeSale()
    {
        return \XLite\Module\CDev\Sale\Logic\PriceBeforeSale::getInstance()->apply($this, 'getNetPriceBeforeSale', array('taxable'), 'display');
    }

    /**
     * Set participateSale
     *
     * @param boolean $participateSale
     * @return Product
     */
    public function setParticipateSale($participateSale)
    {
        $this->participateSale = $participateSale;
        return $this;
    }

    /**
     * Get participateSale
     *
     * @return boolean 
     */
    public function getParticipateSale()
    {
        return $this->participateSale;
    }

    /**
     * Set discountType
     *
     * @param string $discountType
     * @return Product
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;
        return $this;
    }

    /**
     * Set salePriceValue
     *
     * @param decimal $salePriceValue
     * @return Product
     */
    public function setSalePriceValue($salePriceValue)
    {
        $this->salePriceValue = $salePriceValue;
        return $this;
    }

    /**
     * Get salePriceValue
     *
     * @return decimal 
     */
    public function getSalePriceValue()
    {
        return $this->salePriceValue;
    }
}
