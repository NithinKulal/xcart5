<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

/**
 * Abstract multiple attribute value
 * @MappedSuperClass
 */
abstract class Multiple extends \XLite\Model\AttributeValue\AAttributeValue
{
    /**
     * Rate type codes
     */
    const TYPE_ABSOLUTE = 'a';
    const TYPE_PERCENT  = 'p';

    /**
     * Price modifier
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $priceModifier = 0.0000;

    /**
     * Price modifier type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $priceModifierType = self::TYPE_PERCENT;

    /**
     * Weight modifier
     *
     * @var float
     *
     * @Column (type="decimal", precision=14, scale=4)
     */
    protected $weightModifier = 0.0000;

    /**
     * Weight modifier type
     *
     * @var string
     *
     * @Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $weightModifierType = self::TYPE_PERCENT;

    /**
     * Default flag
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $defaultValue = false;

    /**
     * Return modifiers
     *
     * @return array
     */
    public static function getModifiers()
    {
        return [
            'price'  => [
                'title'  => 'Price',
                'symbol' => '$',
            ],
            'weight' => [
                'title'  => 'Weight',
                'symbol' => 'w',
            ],
        ];
    }

    /**
     * Format modifier
     *
     * @param float  $value Value
     * @param string $field Field
     *
     * @return string
     */
    public static function formatModifier($value, $field)
    {
        $method = 'formatModifier' . $field;

        $result = method_exists(__CLASS__, $method)
            ? static::$method($value)
            : $value;

        if (0 < $value) {
            $result = '+' . $result;
        }

        return $result;
    }

    /**
     * Format modifier price
     *
     * @param float $value Value
     *
     * @return string
     */
    public static function formatModifierPrice($value)
    {
        return \XLite\View\Price::getInstance()->formatPrice($value, null, true);
    }

    /**
     * Format modifier weight
     *
     * @param float $value Value
     *
     * @return string
     */
    public static function formatModifierWeight($value)
    {
        return $value . ' ' . \XLite\Core\Translation::translateWeightSymbol();
    }

    /**
     * Set modifier
     *
     * @param string $value Value
     * @param string $field Field
     *
     * @return void
     */
    public function setModifier($value, $field)
    {
        $method = 'set' . $field . 'Modifier';
        if (null !== $value && method_exists($this, $method)) {
            $this->$method((float) $value);

            $method .= 'Type';
            if (method_exists($this, $method)) {
                $this->$method(
                    strpos($value, '%')
                        ? static::TYPE_PERCENT
                        : static::TYPE_ABSOLUTE
                );
            }
        }
    }

    /**
     * Check is default or nor
     *
     * @return boolean
     */
    public function isDefault()
    {
        return $this->getId() === $this->getAttribute()->getDefaultAttributeValue($this->getProduct())->getId();
    }

    /**
     * Get surcharge absolute value
     *
     * @param string $field Field
     *
     * @return float
     */
    public function getAbsoluteValue($field)
    {
        $value = 0;
        if ($this->isApply()) {
            $modifier = $this->getModifierData($field);
            $value    = $this->getModifierBase($field);
            $value    = static::TYPE_PERCENT === $modifier['type']
                ? $value * $modifier['value'] / 100
                : $modifier['value'];
        }

        return round($value, 4);
    }

    /**
     * Get surcharge absolute value
     * @todo: remove in 5.4
     *
     * @param string $field Field
     *
     * @return string
     */
    public function getFormatedValue($field)
    {
        return $this->getFormattedValue($field);
    }

    /**
     * Get surcharge absolute value
     * @todo: check usage
     *
     * @param string $field Field
     *
     * @return string
     */
    public function getFormattedValue($field)
    {
        $modifier = $this->getModifierData($field);
        $value    = $this->getModifierBase($field);
        $value    = static::TYPE_PERCENT === $modifier['type']
            ? $value * $modifier['value'] / 100
            : $modifier['value'];
        $value    = round($value, 2);

        return round($value, 2);
    }

    /**
     * Get modifier
     *
     * @param string $field Field
     *
     * @return mixed
     */
    public function getModifier($field)
    {
        $modifier = $this->getModifierData($field);

        $result = (float) $modifier['value'];
        if ($result) {
            if (0 < $result) {
                $result = '+' . $result;
            }
            if (static::TYPE_PERCENT === $modifier['type']) {
                $result .= '%';
            }

        } else {
            $result = '';
        }

        return $result;
    }

    /**
     * Check is apply or nor
     *
     * @return boolean
     */
    protected function isApply()
    {
        return true;
    }

    /**
     * Get modifier data
     *
     * @param string $field Field
     *
     * @return array
     */
    protected function getModifierData($field)
    {
        $result = [];

        $method          = 'get' . $field . 'Modifier';
        $result['value'] = method_exists($this, $method)
            ? $this->$method()
            : 0;

        $method .= 'Type';
        $result['type'] = method_exists($this, $method)
            ? $this->$method()
            : static::TYPE_ABSOLUTE;

        return $result;
    }

    /**
     * Get modifier base value
     *
     * @param string $field Field
     *
     * @return float
     */
    protected function getModifierBase($field)
    {
        $method = 'getModifierBase' . $field;

        return method_exists($this, $method)
            ? $this->{$method}()
            : 0;
    }

    /**
     * Get price-based modifier base value
     *
     * @return float
     */
    protected function getModifierBasePrice()
    {
        return $this->getProduct()->getClearPrice();
    }

    /**
     * Get weight-based modifier base value
     *
     * @return integer
     */
    protected function getModifierBaseWeight()
    {
        return $this->getProduct()->getWeight();
    }

    /**
     * Set priceModifier
     *
     * @param float $priceModifier
     */
    public function setPriceModifier($priceModifier)
    {
        $this->priceModifier = $priceModifier;
    }

    /**
     * Get priceModifier
     *
     * @return float
     */
    public function getPriceModifier()
    {
        return $this->priceModifier;
    }

    /**
     * Set priceModifierType
     *
     * @param string $priceModifierType
     */
    public function setPriceModifierType($priceModifierType)
    {
        $this->priceModifierType = $priceModifierType;
    }

    /**
     * Get priceModifierType
     *
     * @return string
     */
    public function getPriceModifierType()
    {
        return $this->priceModifierType;
    }

    /**
     * Set weightModifier
     *
     * @param float $weightModifier
     */
    public function setWeightModifier($weightModifier)
    {
        $this->weightModifier = $weightModifier;
    }

    /**
     * Get weightModifier
     *
     * @return float
     */
    public function getWeightModifier()
    {
        return $this->weightModifier;
    }

    /**
     * Set weightModifierType
     *
     * @param string $weightModifierType
     */
    public function setWeightModifierType($weightModifierType)
    {
        $this->weightModifierType = $weightModifierType;
    }

    /**
     * Get weightModifierType
     *
     * @return string
     */
    public function getWeightModifierType()
    {
        return $this->weightModifierType;
    }

    /**
     * Set defaultValue
     *
     * @param boolean $defaultValue
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

    /**
     * Get defaultValue
     *
     * @return boolean
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }
}
