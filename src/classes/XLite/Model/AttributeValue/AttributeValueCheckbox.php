<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\AttributeValue;

/**
 * Attribute value (checkbox)
 *
 * @Entity
 * @Table  (name="attribute_values_checkbox",
 *      indexes={
 *          @Index (name="product_id", columns={"product_id"}),
 *          @Index (name="attribute_id", columns={"attribute_id"}),
 *          @Index (name="value", columns={"value"})
 *      }
 * )
 */
class AttributeValueCheckbox extends \XLite\Model\AttributeValue\Multiple
{
    /**
     * Value
     *
     * @var boolean
     *
     * @Column (type="boolean")
     */
    protected $value = false;

    /**
     * Set value
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        if ('Y' === $value || 1 === $value) {
            $value = true;

        } elseif ('N' === $value || 0 === $value) {
            $value = false;
        }

        $this->value = $value;
    }

    /**
     * Return attribute value as string
     *
     * @return string
     */
    public function asString()
    {
        return static::t($this->getValue() ? 'Yes' : 'No');
    }

    /**
     * Get value
     *
     * @return boolean
     */
    public function getValue()
    {
        return $this->value;
    }
}
