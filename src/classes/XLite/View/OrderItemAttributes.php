<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Order item attributes
 */
class OrderItemAttributes extends \XLite\View\AView
{
    /**
     *  Widget parameters names
     */
    const PARAM_ORDER_ITEM = 'orderItem';
    const PARAM_IDX        = 'idx';
    const PARAM_PRODUCT_ID = 'productId';

    /**
     * Order item (cache)
     *
     * @var \XLite\Model\OrderItem
     */
    protected $orderItem = null;


    /**
     * Get order item entity
     *
     * @return \XLite\Model\OrderItem
     */
    public function getEntity()
    {
        if (is_null($this->orderItem)) {
            $this->orderItem = $this->getParam(self::PARAM_ORDER_ITEM);
        }

        if (is_null($this->orderItem) && $this->getParam(self::PARAM_PRODUCT_ID)) {
            $product = \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getParam(self::PARAM_PRODUCT_ID));

            if ($product) {
                $this->orderItem = new \XLite\Model\OrderItem();
                $this->orderItem->setProduct($product);
                $this->orderItem->setAttributeValues($product->prepareAttributeValues());
            }
        }

        return $this->orderItem;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'items_list/model/table/order_item/cell.name.attributes.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_ORDER_ITEM => new \XLite\Model\WidgetParam\TypeObject('OrderItem', null, false, '\XLite\Model\OrderItem'),
            self::PARAM_PRODUCT_ID => new \XLite\Model\WidgetParam\TypeInt('Product ID', null, false),
            self::PARAM_IDX => new \XLite\Model\WidgetParam\TypeInt('Index of order item', 0, false),

        );
    }

    /**
     * Check widget visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && $this->getEntity()
            && $this->getEntity()->hasAttributeValues()
            && $this->getEntity()->isActualAttributes();
    }

    /**
     * Return true if 'Change options' dialog should be displayed in popover
     *
     * @return boolean
     */
    protected function isPopoverDisplayed()
    {
        $attrsCount = max(
            $this->getEntity()->getAttributeValuesCount(),
            count($this->getEntity()->getProduct()->getEditableAttributes())
        );

        return $this->getPopoverMaxOptions() > $attrsCount;
    }

    /**
     * Get maximum number of options which may be displayed in popover
     *
     * @return integer
     */
    protected function getPopoverMaxOptions()
    {
        return 5;
    }

    /**
     * Get URL of page to display in 'Change options' popup dialog
     *
     * @param array $params Additional parameters
     *
     * @return string
     */
    protected function getOptionsPopupURL($params = array())
    {
        $entity = $this->getEntity();

        return static::buildURL(
            'change_attribute_values',
            null,
            array_merge(
                array(
                    'widget'  => '\XLite\View\ChangeAttributeValues',
                    'item_id' => $entity->getItemId(),
                ),
                $params
            )
        );
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\OrderItem\AttributeValue
     *
     * @return string|integer
     */
    protected function getAttributeId(\XLite\Model\OrderItem\AttributeValue $attrValue)
    {
        $attribute = $attrValue->getAttributeValue() ? $attrValue->getAttributeValue()->getAttribute() : null;

        if (!$attribute && $attrValue->getAttributeId()) {
            $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($attrValue->getAttributeId());
        }

        return $attribute ? $attribute->getId() : $attrValue->getAttributeValueId();
    }

    /**
     * Get attribute input name 
     * 
     * @param \XLite\Model\OrderItem                $entity Order item
     * @param \XLite\Model\OrderItem\AttributeValue $av     Attribute value
     *  
     * @return string
     */
    protected function getAttributeInputName(\XLite\Model\OrderItem $entity, \XLite\Model\OrderItem\AttributeValue $av)
    {
        return $this->getIdx() > 0
            ? 'order_items[' . $entity->getItemId() . '][attribute_values][' . $this->getAttributeId($av) . ']'
            : 'new[' . $this->getIdx() . '][attribute_values][' . $this->getAttributeId($av) . ']';
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\OrderItem\AttributeValue
     *
     * @return string|integer
     */
    protected function getAttributeValue($attrValue)
    {
        $attribute = $attrValue->getAttributeValue() ? $attrValue->getAttributeValue()->getAttribute() : null;

        if (!$attribute && $attrValue->getAttributeId()) {
            $attribute = \XLite\Core\Database::getRepo('XLite\Model\Attribute')->find($attrValue->getAttributeId());
        }

        $attributeType = $attribute ? $attribute->getType() : null;

        $result = null;

        switch ($attributeType) {

            case \XLite\Model\Attribute::TYPE_SELECT:
                $result = $attrValue->getAttributeValueId();
                break;

            case \XLite\Model\Attribute::TYPE_TEXT:
                $result = $attrValue->getValue();
                break;

            case \XLite\Model\Attribute::TYPE_CHECKBOX:
                $result = $attrValue->getAttributeValue()->getValue();
        }

        return $result;
    }

    /**
     * Get order item index
     *
     * @return integer
     */
    protected function getIdx()
    {
        return $this->getParam(static::PARAM_IDX) ?: $this->getEntity()->getItemId();
    }

    /**
     * Get product ID
     *
     * @return integer
     */
    protected function getProductId()
    {
        return $this->getEntity()->getProduct()->getProductId();
    }
}
