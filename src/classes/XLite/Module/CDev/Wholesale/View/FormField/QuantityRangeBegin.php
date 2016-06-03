<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\FormField;

/**
 * Quantity range begin form field
 */
class QuantityRangeBegin extends \XLite\View\FormField\Inline\Input\Text\Integer
{
    /**
     * Register JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/CDev/Wholesale/form_field/quantity.js';

        return $list;
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function isEditable()
    {
        return parent::isEditable() && !$this->getEntity()->isDefaultPrice();
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
        return parent::getFieldParams($field) + array('min' => 1);
    }

    /**
     * getContainerClass 
     * 
     * @return string
     */
    protected function getContainerClass()
    {
        return parent::getContainerClass() . ' inline-quantityRangeBegin';
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getViewTemplate()
    {
        return 'modules/CDev/Wholesale/form_field/quantity_view.twig';
    }
}
