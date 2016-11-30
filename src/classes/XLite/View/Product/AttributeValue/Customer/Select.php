<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Customer;

/**
 * Attribute value (Select)
 */
class Select extends \XLite\View\Product\AttributeValue\Customer\ACustomer
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
     * Get attribute type
     *
     * @return string
     */
    protected function getAttributeType()
    {
        return \XLite\Model\Attribute::TYPE_SELECT;
    }

    /**
     * @return boolean
     */
    protected function showPlaceholderOption()
    {
        if (\XLite\Core\Config::getInstance()->General->force_choose_product_options === 'quicklook') {

            return \XLite::getController()->getTarget() !== 'product';

        } elseif (\XLite\Core\Config::getInstance()->General->force_choose_product_options === 'product_page') {

            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getPlaceholderOptionLabel()
    {
        return static::t('Please select option');
    }

    /**
     * @return array
     */
    protected function getPlaceholderOptionAttributes()
    {
        return [
            'hidden'   => 'hidden',
            'disabled' => 'disabled',
            'selected' => 'selected',
        ];
    }

    /**
     * @return array
     */
    protected function getSelectAttributes()
    {
        $result = [];
        if ($this->showPlaceholderOption()) {
            $result['required'] = 'required';
        }

        return $result;
    }

    /**
     * Return option title
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Value
     *
     * @return string
     */
    protected function getOptionTitle(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        return $value->asString();
    }

    /**
     * Return modifier title
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Value
     *
     * @return string
     */
    protected function getModifierTitle(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        $result = [];
        foreach ($value::getModifiers() as $field => $options) {
            $modifier = $value->getAbsoluteValue($field);
            if (0.0 !== $modifier) {
                $result[] = \XLite\Model\AttributeValue\AttributeValueSelect::formatModifier($modifier, $field);
            }
        }

        return $result
            ? ' (' . implode(', ', $result) . ')'
            : '';
    }

    /**
     * Get option attributes
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Value
     *
     * @return array
     */
    protected function getOptionAttributes(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        $result = [
            'value' => $value->getId(),
        ];

        if ($this->isSelectedValue($value)) {
            $result['selected'] = 'selected';
        }

        foreach ($value::getModifiers() as $field => $options) {
            $modifier = $value->getAbsoluteValue($field);
            if (0 !== $modifier) {
                $result['data-modifier-' . $field] = $modifier;
            }
        }

        return $result;
    }

    /**
     * Return value is selected or not flag
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value Value
     *
     * @return boolean
     */
    protected function isSelectedValue(\XLite\Model\AttributeValue\AttributeValueSelect $value)
    {
        $selectedIds = $this->getSelectedIds();
        $id = $value->getAttribute()->getId();

        $default = $this->showPlaceholderOption()
            ? false
            : $value->isDefault();

        $selected = isset($selectedIds[$id])
            ? (int) $selectedIds[$id] === $value->getId()
            : $default;

        return $selected;
    }

    /**
     * @return string
     */
    protected function getOptionTemplate()
    {
        return 'product/attribute_value/select/option.twig';
    }
}
