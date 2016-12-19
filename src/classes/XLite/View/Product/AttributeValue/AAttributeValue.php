<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue;

use XLite\Core\Cache\ExecuteCachedTrait;

/**
 * Abstract attribute value
 */
abstract class AAttributeValue extends \XLite\View\Product\AProduct
{
    use ExecuteCachedTrait;

    /**
     * Common params
     */
    const PARAM_ATTRIBUTE = 'attribute';
    const PARAM_PRODUCT   = 'product';

    /**
     * Get attribute type
     *
     * @return string
     */
    abstract protected function getAttributeType();

    /**
     * Return field attribute
     *
     * @return \XLite\Model\Attribute
     */
    protected function getAttribute()
    {
        return $this->getParam(self::PARAM_ATTRIBUTE);
    }

    /**
     * @bc: remove in 5.4
     * @return mixed
     */
    protected function getAttrValue()
    {
        return $this->getAttributeValue();
    }

    /**
     * Return field value
     *
     * @return mixed|null
     */
    protected function getAttributeValue()
    {
        return $this->getAttribute()
            ? $this->executeCachedRuntime(function () {
                return $this->defineAttributeValue();
            }, ['getAttributeValue', $this->getAttribute()->getId()])
            : null;
    }

    /**
     * @return mixed|null
     */
    protected function defineAttributeValue()
    {
        return $this->getAttribute()
            ? $this->getAttribute()->getAttributeValue($this->getProduct())
            : null;
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_ATTRIBUTE => new \XLite\Model\WidgetParam\TypeObject(
                'Attribute', null, false, 'XLite\Model\Attribute'
            ),
            static::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject(
                'Product', null, false, 'XLite\Model\Product'
            ),
        );
    }

    /**
     * Is multiple flag
     *
     * @return boolean
     */
    protected function isMultiple()
    {
        return $this->getAttribute()
            && $this->getAttribute()->isMultiple($this->getProduct());
    }

    /**
     * Get style
     *
     * @return string
     */
    protected function getStyle()
    {
        return 'attribute-value type-'
            . strtolower($this->getAttributeType())
            . ($this->isMultiple() ? ' multiple' : '');
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT) ?: \XLite::getController()->getProduct();
    }

    /**
     * Return modifiers
     *
     * @return array
     */
    protected function getModifiers()
    {
        return \XLite\Model\AttributeValue\Multiple::getModifiers();
    }

    /**
     * Get style
     *
     * @param mixed  $attributeValue Attribute value
     * @param string $field          Field
     *
     * @return string
     */
    protected function getModifierValue($attributeValue, $field)
    {
        return $attributeValue
            ? $attributeValue->getModifier($field)
            : '';
    }

    /**
     * Is default flag
     *
     * @param mixed $attributeValue Attribute value
     *
     * @return boolean
     */
    protected function isDefault($attributeValue)
    {
        return $attributeValue
            && is_object($attributeValue)
            && $attributeValue->getDefaultValue();
    }

    /**
     * Get multiple title
     *
     * @return string
     */
    protected function getMultipleTitle()
    {
        return static::t('multi value');
    }
}
