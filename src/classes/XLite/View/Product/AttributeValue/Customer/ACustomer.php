<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Customer;

/**
 * Abstract attribute value (customer)
 */
abstract class ACustomer extends \XLite\View\Product\AttributeValue\AAttributeValue
{
    /**
     * Widget param names
     */
    const PARAM_ORDER_ITEM = 'orderItem';
    const PARAM_NAME_PREFIX = 'namePrefix';
    const PARAM_NAME_SUFFIX = 'nameSuffix';

    /**
     * Selected attribute value ids
     *
     * @var array
     */
    protected $selectedIds = null;

    /**
     * Return field name
     *
     * @return string
     */
    protected function getName()
    {
        return $this->getParam(static::PARAM_NAME_PREFIX)
            . 'attribute_values'
            . $this->getParam(static::PARAM_NAME_SUFFIX)
            . '['
            . $this->getAttribute()->getId()
            . ']';
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
            static::PARAM_ORDER_ITEM => new \XLite\Model\WidgetParam\TypeObject(
                'Order item', null, false, 'XLite\Model\OrderItem'
            ),
            static::PARAM_NAME_PREFIX => new \XLite\Model\WidgetParam\TypeString(
                'Field name prefix', '', false
            ),
            static::PARAM_NAME_SUFFIX => new \XLite\Model\WidgetParam\TypeString(
                'Field name suffix', '', false
            ),
        );
    }

    /**
     * Return field attribute
     *
     * @return \XLite\Model\OrderItem
     */
    protected function getOrderItem()
    {
        return $this->getParam(self::PARAM_ORDER_ITEM);
    }

    /**
     * Return selected attribute values ids
     *
     * @return array
     */
    protected function getSelectedIds()
    {
        if (!isset($this->selectedIds)) {
            $this->selectedIds = array();
            if (
                method_exists($this, 'getSelectedAttributeValuesIds')
                || method_exists(\XLite::getController(), 'getSelectedAttributeValuesIds')
            ) {
                $this->selectedIds = $this->getSelectedAttributeValuesIds();

            } else {

                $item = $this->getOrderItem();

                if (
                    $item
                    && $item->getProduct()
                    && $item->hasAttributeValues()
                ) {
                    $this->selectedIds = $item->getAttributeValuesPlain();
                }
            }
        }

        return $this->selectedIds;
    }

    /**
     * Get list of selected attribute values as array(<attr ID> => <attr value or value ID>)
     *
     * @return array
     */
    protected function getSelectedAttributeValuesIds()
    {
        $result = array();

        $attrValues = $this->getProduct()->getAttrValues();

        if (!empty($attrValues) && \XLite\Model\Attribute::TYPE_TEXT != $this->getAttributeType()) {

            $result = array();

            foreach ($attrValues as $k => $attributeValue) {

                $actualAttributeValue = null;

                if ($attributeValue instanceOf \XLite\Model\OrderItem\AttributeValue) {
                    $actualAttributeValue = $attributeValue->getAttributeValue();

                } elseif ($attributeValue instanceOf \XLite\Model\AttributeValue\AAttributeValue) {
                    $actualAttributeValue = $attributeValue;

                } else {
                    $result[$k] = $attributeValue;
                }

                if ($actualAttributeValue) {
                    if ($actualAttributeValue instanceOf \XLite\Model\AttributeValue\AttributeValueText) {
                        $value = $actualAttributeValue->getValue();

                    } else {
                        $value = $actualAttributeValue->getId();
                    }

                    $result[$actualAttributeValue->getAttribute()->getId()] = $value;
                }
            }

            ksort($result);

        } elseif (method_exists(\XLite::getController(), 'getSelectedAttributeValuesIds')) {
            $result = parent::getSelectedAttributeValuesIds();
        }

        return $result;
    }

    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/attribute_value';
    }
}
