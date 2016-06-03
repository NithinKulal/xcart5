<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Wholesale\View\FormField;

/**
 * Price form field
 */
class Price extends \XLite\View\FormField\Inline\Input\Text\Price
{
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
        return parent::getFieldParams($field) + array('min' => 0);
    }
}
