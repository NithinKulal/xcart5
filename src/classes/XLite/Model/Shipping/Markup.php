<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Shipping;

/**
 * Shipping markup model
 *
 * @Entity
 * @Table (name="shipping_markups",
 *      indexes={
 *          @Index (name="rate", columns={"method_id","zone_id","min_weight","min_total","min_items"}),
 *          @Index (name="max_weight", columns={"max_weight"}),
 *          @Index (name="max_total", columns={"max_total"}),
 *          @Index (name="max_items", columns={"max_items"}),
 *          @Index (name="markup_flat", columns={"markup_flat"}),
 *          @Index (name="markup_per_item", columns={"markup_per_item"}),
 *          @Index (name="markup_percent", columns={"markup_percent"}),
 *          @Index (name="markup_per_weight", columns={"markup_per_weight"})
 *      }
 * )
 */
class Markup extends \XLite\Model\AEntity
{
    const INFINITY_VALUE = 999999999;

    /**
     * A unique ID of the markup
     *
     * @var integer
     *
     * @Id
     * @GeneratedValue (strategy="AUTO")
     * @Column (type="integer")
     */
    protected $markup_id;

    /**
     * Markup condition: min weight of products in the order
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=2)
     */
    protected $min_weight = 0;

    /**
     * Markup condition: max weight of products in the order
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=2)
     */
    protected $max_weight = self::INFINITY_VALUE;

    /**
     * Markup condition: min order subtotal
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=2)
     */
    protected $min_total = 0;

    /**
     * Markup condition: max order subtotal
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=2)
     */
    protected $max_total = self::INFINITY_VALUE;

    /**
     * Markup condition: min product items in the order
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=0)
     */
    protected $min_items = 0;

    /**
     * Markup condition: max product items in the order
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=0)
     */
    protected $max_items = self::INFINITY_VALUE;

    /**
     * Markup value: flat rate value
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=2)
     */
    protected $markup_flat = 0;

    /**
     * Markup value: percent value
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=2)
     */
    protected $markup_percent = 0;

    /**
     * Markup value: flat rate value per product item
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=2)
     */
    protected $markup_per_item = 0;

    /**
     * Markup value: flat rate value per weight unit
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=2)
     */
    protected $markup_per_weight = 0;

    /**
     * Shipping method (relation)
     *
     * @var \XLite\Model\Shipping\Method
     *
     * @ManyToOne  (targetEntity="XLite\Model\Shipping\Method", inversedBy="shipping_markups")
     * @JoinColumn (name="method_id", referencedColumnName="method_id", onDelete="CASCADE")
     */
    protected $shipping_method;

    /**
     * Zone (relation)
     *
     * @var \XLite\Model\Zone
     *
     * @ManyToOne  (targetEntity="XLite\Model\Zone", inversedBy="shipping_markups", cascade={"merge"})
     * @JoinColumn (name="zone_id", referencedColumnName="zone_id", onDelete="CASCADE")
     */
    protected $zone;

    /**
     * Calculated markup value
     *
     * @var float
     */
    protected $markupValue = 0;

    /**
     * getMarkupValue
     *
     * @return float
     */
    public function getMarkupValue()
    {
        return $this->markupValue;
    }

    /**
     * setMarkupValue
     *
     * @param integer $value Markup value
     *
     * @return void
     */
    public function setMarkupValue($value)
    {
        $this->markupValue = $value;
    }

    /**
     * Has rates
     *
     * @return boolean
     */
    public function hasRates()
    {
        return true;
    }

    /**
     * Returns weight range
     *
     * @return array
     */
    public function getWeightRange()
    {
        return array(
            $this->getMinWeight(),
            $this->getMaxWeight() == static::INFINITY_VALUE ? html_entity_decode('&#x221E;') : $this->getMaxWeight()
        );
    }

    /**
     * Set weight range
     *
     * @param array $value value
     *
     * @return array
     */
    public function setWeightRange($value)
    {
        if (is_array($value)) {
            $this->setMinWeight($value[0]);
            $this->setMaxWeight($value[1] === html_entity_decode('&#x221E;') ? static::INFINITY_VALUE : $value[1]);
        }
    }

    /**
     * Returns subtotal range
     *
     * @return array
     */
    public function getSubtotalRange()
    {
        return array(
            $this->getMinTotal(),
            $this->getMaxTotal() == static::INFINITY_VALUE ? html_entity_decode('&#x221E;') : $this->getMaxTotal()
        );
    }

    /**
     * Set subtotal range
     *
     * @param array $value value
     *
     * @return array
     */
    public function setSubtotalRange($value)
    {
        if (is_array($value)) {
            $this->setMinTotal($value[0]);
            $this->setMaxTotal($value[1] === html_entity_decode('&#x221E;') ? static::INFINITY_VALUE : $value[1]);
        }
    }

    /**
     * Returns items range
     *
     * @return array
     */
    public function getItemsRange()
    {
        return array(
            $this->getMinItems(),
            $this->getMaxItems() == static::INFINITY_VALUE ? html_entity_decode('&#x221E;') : $this->getMaxItems()
        );
    }

    /**
     * Set items range
     *
     * @param array $value value
     *
     * @return array
     */
    public function setItemsRange($value)
    {
        if (is_array($value)) {
            $this->setMinItems($value[0]);
            $this->setMaxItems($value[1] === html_entity_decode('&#x221E;') ? static::INFINITY_VALUE : $value[1]);
        }
    }

    /**
     * Get markup_id
     *
     * @return integer 
     */
    public function getMarkupId()
    {
        return $this->markup_id;
    }

    /**
     * Set min_weight
     *
     * @param decimal $minWeight
     * @return Markup
     */
    public function setMinWeight($minWeight)
    {
        $this->min_weight = $minWeight;
        return $this;
    }

    /**
     * Get min_weight
     *
     * @return decimal 
     */
    public function getMinWeight()
    {
        return $this->min_weight;
    }

    /**
     * Set max_weight
     *
     * @param decimal $maxWeight
     * @return Markup
     */
    public function setMaxWeight($maxWeight)
    {
        $this->max_weight = $maxWeight;
        return $this;
    }

    /**
     * Get max_weight
     *
     * @return decimal 
     */
    public function getMaxWeight()
    {
        return $this->max_weight;
    }

    /**
     * Set min_total
     *
     * @param decimal $minTotal
     * @return Markup
     */
    public function setMinTotal($minTotal)
    {
        $this->min_total = $minTotal;
        return $this;
    }

    /**
     * Get min_total
     *
     * @return decimal 
     */
    public function getMinTotal()
    {
        return $this->min_total;
    }

    /**
     * Set max_total
     *
     * @param decimal $maxTotal
     * @return Markup
     */
    public function setMaxTotal($maxTotal)
    {
        $this->max_total = $maxTotal;
        return $this;
    }

    /**
     * Get max_total
     *
     * @return decimal 
     */
    public function getMaxTotal()
    {
        return $this->max_total;
    }

    /**
     * Set min_items
     *
     * @param decimal $minItems
     * @return Markup
     */
    public function setMinItems($minItems)
    {
        $this->min_items = $minItems;
        return $this;
    }

    /**
     * Get min_items
     *
     * @return decimal 
     */
    public function getMinItems()
    {
        return $this->min_items;
    }

    /**
     * Set max_items
     *
     * @param decimal $maxItems
     * @return Markup
     */
    public function setMaxItems($maxItems)
    {
        $this->max_items = $maxItems;
        return $this;
    }

    /**
     * Get max_items
     *
     * @return decimal 
     */
    public function getMaxItems()
    {
        return $this->max_items;
    }

    /**
     * Set markup_flat
     *
     * @param decimal $markupFlat
     * @return Markup
     */
    public function setMarkupFlat($markupFlat)
    {
        $this->markup_flat = $markupFlat;
        return $this;
    }

    /**
     * Get markup_flat
     *
     * @return decimal 
     */
    public function getMarkupFlat()
    {
        return $this->markup_flat;
    }

    /**
     * Set markup_percent
     *
     * @param decimal $markupPercent
     * @return Markup
     */
    public function setMarkupPercent($markupPercent)
    {
        $this->markup_percent = $markupPercent;
        return $this;
    }

    /**
     * Get markup_percent
     *
     * @return decimal 
     */
    public function getMarkupPercent()
    {
        return $this->markup_percent;
    }

    /**
     * Set markup_per_item
     *
     * @param decimal $markupPerItem
     * @return Markup
     */
    public function setMarkupPerItem($markupPerItem)
    {
        $this->markup_per_item = $markupPerItem;
        return $this;
    }

    /**
     * Get markup_per_item
     *
     * @return decimal 
     */
    public function getMarkupPerItem()
    {
        return $this->markup_per_item;
    }

    /**
     * Set markup_per_weight
     *
     * @param decimal $markupPerWeight
     * @return Markup
     */
    public function setMarkupPerWeight($markupPerWeight)
    {
        $this->markup_per_weight = $markupPerWeight;
        return $this;
    }

    /**
     * Get markup_per_weight
     *
     * @return decimal 
     */
    public function getMarkupPerWeight()
    {
        return $this->markup_per_weight;
    }

    /**
     * Set shipping_method
     *
     * @param \XLite\Model\Shipping\Method $shippingMethod
     * @return Markup
     */
    public function setShippingMethod(\XLite\Model\Shipping\Method $shippingMethod = null)
    {
        $this->shipping_method = $shippingMethod;
        return $this;
    }

    /**
     * Get shipping_method
     *
     * @return \XLite\Model\Shipping\Method 
     */
    public function getShippingMethod()
    {
        return $this->shipping_method;
    }

    /**
     * Set zone
     *
     * @param \XLite\Model\Zone $zone
     * @return Markup
     */
    public function setZone(\XLite\Model\Zone $zone = null)
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * Get zone
     *
     * @return \XLite\Model\Zone 
     */
    public function getZone()
    {
        return $this->zone;
    }
}
