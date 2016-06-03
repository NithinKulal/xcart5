<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Admin;

/**
 * Attribute value (Checkbox)
 */
class Checkbox extends \XLite\View\Product\AttributeValue\Admin\AAdmin
{
    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/checkbox';
    }

    /**
     * Get attribute type
     *
     * @return string
     */
    protected function getAttributeType()
    {
        return \XLite\Model\Attribute::TYPE_CHECKBOX;
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
                $result[intval($v->getValue())] = $v;
            }
            unset($values);
        }

        foreach (array(0, 1) as $v) {
            if (!isset($result[$v])) {
                $result[$v] = null;
            }
        }

        ksort($result);

        return $result;
    }

    /**
     * Return select value
     *
     * @return boolean
     */
    protected function getSelectValue()
    {
        $value = $this->getAttrValue();
        if (is_array($value)) {
            if (!empty($value)) {
                foreach ($value as $v) {
                    if ($v) {
                        $value = $v;
                        break;
                    }
                }

            } else {
                $value = '';
            }
        }

        return is_object($value)
            ? $value->getValue()
            : $value;
    }

    /**
     * Return label
     *
     * @var indegeer $id Id
     *
     * @return string
     */
    protected function getLabel($id)
    {
        return static::t($id ? 'Yes' : 'No');
    }

    /**
     * Return name of widget class
     *
     * @return string
     */
    protected function getWidgetClass()
    {
        return $this->getAttribute() && !$this->getAttribute()->getProduct()
            ? '\XLite\View\FormField\Select\YesNoEmpty'
            : '\XLite\View\FormField\Select\YesNo';
    }
}
