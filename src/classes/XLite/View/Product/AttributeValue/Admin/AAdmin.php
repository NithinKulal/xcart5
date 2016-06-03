<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\AttributeValue\Admin;

/**
 * Abstract attribute value (admin)
 */
abstract class AAdmin extends \XLite\View\Product\AttributeValue\AAttributeValue
{
    /**
     * Return field name
     *
     * @param string $field Field OPTIONAL
     * @param string $id    Id OPTIONAL
     *
     * @return string
     */
    protected function getName($field = 'value', $id = null)
    {
        $name = $this->getAttribute()
            ? 'attributeValue[' . $this->getAttribute()->getId() . ']'
            : 'newValue[NEW_ID]';

        $name .= '[' . $field . ']';

        if (isset($id)) {
            $name .= '[' . $id . ']';
        }

        return $name;
    }

    /**
     * Get modifiers as string
     *
     * @param mixed $attributeValue Aattribute value
     *
     * @return string
     */
    protected function getModifiersAsString($attributeValue)
    {
        $result = '';

        if ($attributeValue) {
            foreach ($this->getModifiers() as $field => $modifier) {
                $str = $this->getModifierValue($attributeValue, $field);
                if ($str) {
                    $result .= ' <span class="' . $field . '-modifier">' . trim($str) . '</span>';
                }
            }
            if ($this->isDefault($attributeValue)) {
                $result = static::t('Default') . ($result ? ', ' : '') . $result;
            }
        }

        return $result;
    }

    /**
     * Check attribute is modified or not
     *
     * @return boolean
     */
    protected function isModified()
    {
        return true;
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

    /**
     * Get dir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/manage_attribute_value';
    }

    /**
     * Get JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = parent::getDir() . '/manage_attribute_value/controller.js';

        return $list;
    }
}
