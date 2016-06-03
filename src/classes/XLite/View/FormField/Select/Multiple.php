<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Form multiple selector
 */
class Multiple extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Set value
     *
     * @param mixed $value Value to set
     *
     * @return void
     */
    public function setValue($value)
    {
        if (is_object($value) && $value instanceof \Doctrine\Common\Collections\Collection) {
            $value = $value->toArray();

        } elseif (!is_array($value)) {
            $value = $value ? array($value) : array();
        }

        foreach ($value as $k => $v) {
            if (is_object($v) && $v instanceof \XLite\Model\AEntity) {
                $value[$k] = $v->getUniqueIdentifier();
            }
        }

        parent::setValue($value);
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array();
    }

    /**
     * Set common attributes
     *
     * @param array $attrs Field attributes to prepare
     *
     * @return array
     */
    protected function setCommonAttributes(array $attrs)
    {
        return parent::setCommonAttributes($attrs) + array('multiple' => 'multiple');
    }

    /**
     * Check - current value is selected or not
     *
     * @param mixed $value Value
     *
     * @return boolean
     */
    protected function isOptionSelected($value)
    {
        return $this->getValue() && in_array($value, $this->getValue());
    }

    /**
     * Get common attributes
     *
     * @return array
     */
    protected function getCommonAttributes()
    {
        $list = parent::getCommonAttributes();

        $list['name'] .= '[]';

        return $list;
    }

    /**
     * Assemble classes
     *
     * @param array $classes Classes
     *
     * @return array
     */
    protected function assembleClasses(array $classes)
    {
        $classes = parent::assembleClasses($classes);
        $classes[] = 'multiple-selector';

        return $classes;
    }
}
