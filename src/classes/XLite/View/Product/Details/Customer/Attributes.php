<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer;

use XLite\View\CacheableTrait;

/**
 * Product attributes
 */
class Attributes extends \XLite\View\Product\Details\AAttributes
{
    use CacheableTrait;

    /**
     * Attributes
     *
     * @var array
     */
    protected $attributes;

    /**
     * Get attribute list
     *
     * @return array
     */
    public function getAttrList()
    {
        if (!isset($this->attributes)) {
            $this->attributes = array();
            foreach ($this->getAttributesList() as $a) {
                $value = $a->getAttributeValue($this->getProduct(), true);
                if (is_array($value)) {
                    $value = implode($a::DELIMITER, $value);
                }
                if (
                    $value
                    && (
                        $a::TYPE_CHECKBOX != $a->getType()
                        || static::t('No') != $value
                    )
                ) {
                    $this->attributes[] = array(
                        'name'  => $a->getName(),
                        'value' => ($a::TYPE_TEXT == $a->getType() ? $value : htmlspecialchars($value)),
                        'class' => $this->getFieldClass($a, $value)
                    );
                }
            }
        }

        return $this->attributes;
    }

    /**
     * Check if attribute list is empty
     *
     * @return bool
     */
    public function isAttrListEmpty()
    {
        $cacheParams   = $this->getCacheParameters();
        $cacheParams[] = 'isAttrListEmpty';

        return $this->executeCached(function () {
            return count($this->getAttrList()) == 0;
        }, $cacheParams);
    }

    /**
     * Return field class
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     * @param string                 $value     Value
     *
     * @return string
     */
    protected function getFieldClass(\XLite\Model\Attribute $attribute, $value)
    {
        $class = str_replace(' ', '-', strtolower($attribute->getTypes($attribute->getType())));
        if (\XLite\Model\Attribute::TYPE_CHECKBOX == $attribute->getType()) {
            $class .= ' ' . (static::t('Yes') == $value ? 'checked' : 'no-checked');
        }

        return $class;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'product/details/parts/attribute.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && !$this->isAttrListEmpty();
    }

    /**
     * Postprocess attributes
     *
     * @param array $attributes Attributes
     *
     * @return array
     */
    protected function postprocessAttributes(array $attributes)
    {
        $attributes = parent::postprocessAttributes($attributes);

        $product = $this->getProduct();
        if ($product && !\XLite::isAdminZone()) {
            foreach ($attributes as $i => $attribute) {
                $value = $attribute->getAttributeValue($product);
                if (
                    $value
                    && $value instanceOf \XLite\Model\AttributeValue\AttributeValueText
                    && $value->getEditable()
                ) {
                    unset($attributes[$i]);
                }
            }
            $attributes = array_values($attributes);
        }

        return $attributes;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $productId = $this->getProduct()->getId();

        $list[] = $productId;

        $widgetParams = [
            'personalOnly',
            'productClass',
            'group',
        ];

        foreach ($widgetParams as $param) {
            $parameter = $this->getWidgetParams($param);
            $value = $parameter ? $parameter->value : null;
            $list[] = !is_object($value) ? $value : $value->getUniqueIdentifier();
        }

        // We don't need a dependence on cart items here:
        // $list[] = $cart->getItemsFingerprintByProductId($productId);

        return $list;
    }
}
