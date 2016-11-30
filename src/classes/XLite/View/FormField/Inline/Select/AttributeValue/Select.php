<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Select\AttributeValue;

/**
 * Select
 */
class Select extends \XLite\View\FormField\Inline\Base\Single
{
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'form_field/inline/select/attribute_value/select/controller.js';

        return $list;
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'form_field/inline/select/attribute_value/select/style.css';

        return $list;
    }

    /**
     * @return array
     */
    protected function getCommonFiles()
    {
        return [
            \XLite\View\AView::RESOURCE_JS  => ['select2/dist/js/select2.min.js', 'js/select2.sortable.js'],
            \XLite\View\AView::RESOURCE_CSS => ['select2/dist/css/select2.min.css'],
        ];
    }

    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Select\AttributeValue\Select';
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function hasSeparateView()
    {
        return false;
    }

    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $list = parent::getFieldParams($field);

        /** @var \XLite\Model\AttributeValue\AttributeValueSelect[] $options */
        $options = $list['value'];

        $result = [];
        foreach ($options as $option) {
            $result[(string) $option->getId()] = $option->asString();
        }

        $list['options'] = $result;

        return $list;
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        $result = [];

        if (isset($field['value']) && is_array($field['value'])) {
            /** @var \XLite\Model\AttributeValue\AttributeValueSelect[] $options */
            $options = $field['value'];
            foreach ($options as $option) {
                $result[] = $option->getId();
            }
        }

        return $result;
    }

    /**
     * Save field value to entity
     *
     * @param array $field Field
     * @param array $value Value
     *
     * @return void
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        $position = [];
        $counter = 0;
        foreach ($value as $valueId) {
            $position[$valueId] = $counter += 10;
        }

        /** @var \XLite\Model\Attribute $entity */
        $entity = $this->getEntity();
        $fieldParams = $this->getParam(self::PARAM_FIELD_PARAMS);
        $product = $fieldParams['product'];

        /** @var \XLite\Model\AttributeValue\AttributeValueSelect[] $options */
        $options = $entity->getAttributeValue($product);
        foreach ($options as $attributeValue) {
            $attributeValue->setPosition($position[$attributeValue->getId()]);
        }
    }
}
