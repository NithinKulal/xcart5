<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductVariants\View\FormField\Inline\Input\Text;

/**
 * Default value
 */
class DefaultValue extends \XLite\View\FormField\Inline\Input\Text
{
    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        return parent::getFieldParams($field) + array('min' => 0, 'mouseWheelIcon' => false, 'placeholder' => static::t('Default'));
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
        $method = 'getDefault' . ucfirst($field[static::FIELD_NAME]);

        return !$this->getEntity()->$method() ? parent::getFieldEntityValue($field) : '';
    }

    /**
     * Save value
     *
     * @return void
     */
    public function saveValue()
    {
        foreach ($this->getFields() as $field) {
            if ('' === $field['widget']->getValue()) {
                $defaultValue = true;
                $field['widget']->setValue($this->getEmptyFieldValue());

            } else {
                $defaultValue = false;
            }

            $method = 'setDefault' . ucfirst($field['field'][static::FIELD_NAME]);
            $this->getEntity()->$method($defaultValue);
        }

        parent::saveValue();
    }

    /**
     * Get value to write to the database when default value is used (to avoid errors when MySQL works in strict mode)
     *
     * @return integer
     */
    protected function getEmptyFieldValue()
    {
        return 0;
    }
}
