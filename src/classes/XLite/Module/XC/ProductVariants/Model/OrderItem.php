<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\Model;

/**
 * Something customer can put into his cart
 *
 */
class OrderItem extends \XLite\Model\OrderItem implements \XLite\Base\IDecorator
{
    /**
     * Product variant
     *
     * @var \XLite\Module\XC\ProductVariants\Model\ProductVariant
     *
     * @ManyToOne  (targetEntity="XLite\Module\XC\ProductVariants\Model\ProductVariant", inversedBy="orderItems", cascade={"merge","detach"})
     * @JoinColumn (name="variant_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $variant;

    /**
     * Get item clear price. This value is used as a base item price for calculation of netPrice
     *
     * @return float
     */
    public function getClearPrice()
    {
        return $this->getVariant()
            ? $this->getVariant()->getClearPrice()
            : parent::getClearPrice();
    }

    /**
     * Get item weight
     *
     * @return float
     */
    public function getClearWeight()
    {
        return $this->getVariant()
            ? $this->getVariant()->getClearWeight()
            : parent::getClearWeight();
    }

    /**
     * Check if item is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $result = parent::isValid();

        if (
            $result
            && (
                $this->getProduct()->mustHaveVariants()
                || $this->getVariant()
            )
        ) {
            $variant = $this->getProduct()->getVariantByAttributeValuesIds($this->getAttributeValuesIds());
            $result = $variant
                && $this->getVariant()
                && $variant->getId() == $this->getVariant()->getId()
                && (
                    !$this->product->getInventoryEnabled()
                    || $this->getVariant()->getDefaultAmount()
                    || 0 < $variant->getAmount()
                );
        }

        return $result;
    }

    /**
     * Check if item has a wrong amount
     *
     * @return boolean
     */
    public function hasWrongAmount()
    {
        return $this->getVariant() && !$this->getVariant()->getDefaultAmount()
            ? $this->getVariant()->getPublicAmount() < $this->getAmount()
            : parent::hasWrongAmount();
    }

    /**
     * Renew order item
     *
     * @return boolean
     */
    public function renew()
    {
        $available = parent::renew();

        if ($available && $this->getVariant()) {
            $this->setSKU($this->getVariant()->getDisplaySku());
            $this->setPrice($this->getVariant()->getDisplayPrice());
        }

        return $available;
    }

    /**
     * Get inventory amount of this item
     *
     * @return int
     */
    public function getInventoryAmount()
    {
        return $this->getVariant() && !$this->getVariant()->getDefaultAmount()
            ? $this->getVariant()->getAmount()
            : parent::getInventoryAmount();
    }

    /**
     * Increase / decrease product inventory amount
     *
     * @param integer $delta Amount delta
     *
     * @return void
     */
    public function changeAmount($delta)
    {
        if ($this->getVariant() && !$this->getVariant()->getDefaultAmount()) {
            $this->getVariant()->changeAmount($delta);

        } else {
            parent::changeAmount($delta);
        }
    }

    /**
     * Check - item price is controlled by server or not
     *
     * @return boolean
     */
    public function isPriceControlledServer()
    {
        return parent::isPriceControlledServer()
            || ($this->getProduct() && $this->getProduct()->hasVariants());
    }

    /**
     * Check item amount
     *
     * @return boolean
     */
    protected function checkAmount()
    {
        return $this->getVariant() && !$this->getVariant()->getDefaultAmount()
            ? $this->getVariant()->getAvailableAmount() >= 0
            : parent::checkAmount();
    }

    /**
     * Check - can change item's amount or not
     *
     * @return boolean
     */
    public function canChangeAmount()
    {
        $product = $this->getProduct();

        if (
            $product
            && $product->getInventoryEnabled()
            && $this->getVariant()
            && !$this->getVariant()->getDefaultAmount()
        ) {
            $result = (0 < $this->getVariant()->getAmount());

        } else {
            $result = parent::canChangeAmount();
        }

        return $result;
    }

    /**
     * Return extended item description
     *
     * @return string
     */
    public function getExtendedDescription()
    {
        $result = '';

        if ($this->getVariant()) {
            $attrs = $variantsAttributes = array();
            foreach ($this->getProduct()->getVariantsAttributes() as $a) {
                $variantsAttributes[$a->getId()] = $a->getId();
            }

            foreach ($this->getAttributeValues() as $v) {
                $av = $v->getAttributeValue();
                if ($av->getAttribute()->isVariable($this->getProduct())) {
                    $attrs[] = $av->getAttribute()->getName() . ': ' . $av->asString();
                }
            }

            $result = '(' . implode(', ', $attrs) . ')';
        }

        return $result ?: parent::getExtendedDescription();
    }

    /**
     * Get available amount for the product
     *
     * @return integer
     */
    public function getProductAvailableAmount()
    {
        return $this->getVariant() && !$this->getVariant()->getDefaultAmount()
            ? $this->getVariant()->getAvailableAmount()
            : parent::getProductAvailableAmount();
    }

    /**
     * Clone order item object
     *
     * @return \XLite\Model\OrderItem
     */
    public function cloneEntity()
    {
        $newItem = parent::cloneEntity();

        if ($this->getVariant()) {
            $newItem->setVariant($this->getVariant());
        }

        return $newItem;
    }

    /**
     * Get item image
     *
     * @return \XLite\Model\Base\Image
     */
    public function getImage()
    {
        $image = $this->getVariant() ? $this->getVariant()->getImage() : null;

        if (!$image) {
            $image = $this->getProduct()->getProductImage();
        }

        if (!$image && $this->getProduct()->getDefaultVariant() && $this->getProduct()->getDefaultVariant()->getImage()) {
            $image = $this->getProduct()->getDefaultVariant()->getImage();
        }

        return $image ?: parent::getImage();
    }

    /**
     * Set variant
     *
     * @param \XLite\Module\XC\ProductVariants\Model\ProductVariant $variant
     * @return OrderItem
     */
    public function setVariant(\XLite\Module\XC\ProductVariants\Model\ProductVariant $variant = null)
    {
        $this->variant = $variant;
        return $this;
    }

    /**
     * Get variant
     *
     * @return \XLite\Module\XC\ProductVariants\Model\ProductVariant 
     */
    public function getVariant()
    {
        return $this->variant;
    }
}
