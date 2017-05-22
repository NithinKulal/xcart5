<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Order\Modifier;

/**
 * Abstract order modifier
 */
abstract class AModifier extends \XLite\Logic\ALogic
{
    /**
     * Mode codes
     */
    const MODE_CART  = 'cart';
    const MODE_ORDER = 'order';

    /**
     * Modifier type (see \XLite\Model\Base\Surcharge)
     *
     * @var string
     */
    protected $type;

    /**
     * Modifier unique code
     *
     * @var string
     */
    protected $code;

    /**
     * Model
     *
     * @var \XLite\Model\Order\Modifier
     */
    protected $model;

    /**
     * Order
     *
     * @var \XLite\Model\Order
     */
    protected $order;

    /**
     * Modifiers list
     *
     * @var \XLite\DataSet\Collection\OrderModifier
     */
    protected $list;

    /**
     * Surcharge identification pattern
     *
     * @var string
     */
    protected $identificationPattern;

    /**
     * Mode
     *
     * @var string
     */
    protected $mode;

    /**
     * Sorting weight
     *
     * @var integer
     */
    protected $sortingWeight = 0;

    /**
     * Calculate and return added surcharge or array of surcharges
     *
     * @return \XLite\Model\Order\Surcharge|array
     */
    abstract public function calculate();

    /**
     * Get surcharge information
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return \XLite\DataSet\Transport\Order\Surcharge
     */
    abstract public function getSurchargeInfo(\XLite\Model\Base\Surcharge $surcharge);


    // {{{ Widget

    /**
     * Get widget class
     *
     * @return string
     */
    public static function getWidgetClass()
    {
        return '\XLite\View\Order\Details\Admin\Modifier';
    }

    // }}}

    /**
     * Constructor
     *
     * @param \XLite\Model\Order\Modifier $model Model
     */
    public function __construct(\XLite\Model\Order\Modifier $model)
    {
        $this->model = $model;
    }

    /**
     * Initialize modifier
     *
     * @param \XLite\Model\Order                      $order Context
     * @param \XLite\DataSet\Collection\OrderModifier $list  Modifiers list
     *
     * @return void
     */
    public function initialize(\XLite\Model\Order $order, \XLite\DataSet\Collection\OrderModifier $list)
    {
        $this->order = $order;
        $this->list = $list;
    }

    /**
     * Return true if surcharges created after calculate() should be processed to remove duplicates
     * See XLite\View\FormField\Inline\Input\Text\Price\OrderModifierTotal::saveFieldEntityValue()
     *
     * @return boolean
     */
    public function isIgnoreDuplicates()
    {
        return false;
    }

    /**
     * Preprocess internal state
     *
     * @return void
     */
    public function preprocess()
    {
    }

    /**
     * Check - can apply this modifier or not
     *
     * @return boolean
     */
    public function canApply()
    {
        return $this->type && $this->code && $this->order && $this->list && 0 < count($this->list);
    }

    /**
     * Get modifier type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get modifier unique code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set modifier mode
     *
     * @param string $mode Mode OPTIONAL
     *
     * @return void
     */
    public function setMode($mode = null)
    {
        $this->mode = $mode;
    }

    /**
     * Get modifier mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Return weight
     *
     * @return integer
     */
    public function getSortingWeight()
    {
        return $this->sortingWeight;
    }

    /**
     * Check - order is cart
     *
     * @return boolean
     */
    protected function isCart()
    {
        return $this->getMode()
            ? $this->getMode() === static::MODE_CART
            : $this->order instanceof \XLite\Model\Cart;
    }

    // {{{ Surcharge operations

    /**
     * Check - modifier is specified surcharge owner or not
     *
     * @param \XLite\Model\Base\Surcharge $surcharge Surcharge
     *
     * @return boolean
     */
    public function isSurchargeOwner(\XLite\Model\Base\Surcharge $surcharge)
    {
        return $this->isCodeApplicable($surcharge->getCode());
    }

    /**
     * Check - is code applicable to this modifier or not
     * 
     * @param $code
     *
     * @return bool
     */
    public function isCodeApplicable($code)
    {
        return ($this->identificationPattern && preg_match($this->identificationPattern, $code))
        || $code === $this->getCode();
    }

    /**
     * Add order surcharge
     *
     * @param string  $code      Surcharge code
     * @param float   $value     Value
     * @param boolean $include   Include flag OPTIONAL
     * @param boolean $available Availability flag OPTIONAL
     *
     * @return \XLite\Model\Order\Surcharge
     */
    public function addOrderSurcharge($code, $value, $include = false, $available = true)
    {
        $surcharge = new \XLite\Model\Order\Surcharge;

        $surcharge->setType($this->type);
        $surcharge->setCode($code);
        $surcharge->setValue($value);
        $surcharge->setInclude($include);
        $surcharge->setAvailable($available);
        $surcharge->setClass(get_called_class());

        $info = $this->getSurchargeInfo($surcharge);
        $surcharge->setName($info->name);

        $surcharge->setWeight(count($this->order->getSurcharges()));
        $this->order->addSurcharges($surcharge);
        $surcharge->setOwner($this->order);

        return $surcharge;
    }

    /**
     * Add order item surcharge
     *
     * @param \XLite\Model\OrderItem $item      Order item
     * @param string                 $code      Surcharge code
     * @param float                  $value     Value
     * @param boolean                $include   Include flag OPTIONAL
     * @param boolean                $available Availability flag OPTIONAL
     *
     * @return \XLite\Model\OrderItem\Surcharge
     */
    protected function addOrderItemSurcharge(
        \XLite\Model\OrderItem $item,
        $code,
        $value,
        $include = false,
        $available = true
    ) {
        $surcharge = new \XLite\Model\OrderItem\Surcharge;

        $surcharge->setType($this->type);
        $surcharge->setCode($code);
        $surcharge->setValue($value);
        $surcharge->setInclude($include);
        $surcharge->setAvailable($available);
        $surcharge->setClass(get_called_class());

        $info = $this->getSurchargeInfo($surcharge);
        $surcharge->setName($info->name);

        $item->addSurcharges($surcharge);
        $surcharge->setOwner($item);

        return $surcharge;
    }

    // }}}
}
