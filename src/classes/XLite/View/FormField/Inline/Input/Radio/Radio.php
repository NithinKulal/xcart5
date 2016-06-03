<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Radio;

/**
 * Radio button
 */
class Radio extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\FormField\Input\Radio';
    }

    /**
     * Get container class
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-radio';
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
        $params = parent::getFieldParams($field);

        $params[\XLite\View\FormField\Input\Radio::PARAM_IS_CHECKED] = $this->getEntityValue();

        if (method_exists($this->getEntity(), 'getEnabled')
            && !$this->getEntity()->getEnabled()
        ) {
            $params['attributes'] = array(
                'disabled' => 'disabled'
            );
        }

        return $params;
    }

    /**
     * Preprocess value before save: return 1 or 0
     *
     * @param mixed $value Value
     *
     * @return integer
     */
    protected function preprocessValueBeforeSave($value)
    {
        return intval($value);
    }

    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return string
     */   
    protected function getFieldEntityValue(array $field)
    {
        return $this->getEntity()->getCode();
    }
}
