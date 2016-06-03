<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Admin;

/**
 * Attribute value (Select)
 */
class Select extends \XLite\View\Product\AttributeValue\Admin\AAdmin
{
    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/select';
    }

    /**
     * Return values
     *
     * @return array
     */
    protected function getAttrValues()
    {
        $values = $this->getAttrValue();

        if ($values) {
            $result = array();
            foreach ($values as $v) {
                $result[$v->getId()] = $v;
            }
            unset($values);

        } else {
            $result = array(null);
        }
        $result['NEW_ID'] = null;

        return $result;
    }

    /**
     * Get attribute type
     *
     * @return string
     */
    protected function getAttributeType()
    {
        return \XLite\Model\Attribute::TYPE_SELECT;
    }

    /**
     * Return name of widget class
     *
     * @return string
     */
    protected function getWidgetClass()
    {
        return $this->getAttribute() && !$this->getAttribute()->getProduct()
            ? '\XLite\View\FormField\Input\Text\AttributeOption'
            : '\XLite\View\FormField\Input\Text';
    }

    /**
     * Return field value
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $attributeValue Attribute value
     *
     * @return mixed
     */
    protected function getFieldValue($attributeValue)
    {
        return $attributeValue && $this->getAttribute() && $this->getAttribute()->getProduct()
            ? $attributeValue->getAttributeOption()->getName()
            : $attributeValue;
    }

    /**
     * Get value style
     *
     * @param $id Id
     *
     * @return string
     */
    protected function getValueStyle($id)
    {
        return 'line clearfix '
            . (is_int($id) ? 'value' : 'new');
    }
}
