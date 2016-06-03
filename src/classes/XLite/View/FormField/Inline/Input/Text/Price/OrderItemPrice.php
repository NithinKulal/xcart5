<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Price;

/**
 * Order item price widget for AOM
 */
class OrderItemPrice extends \XLite\View\FormField\Inline\Input\Text\Price
{
    /**
     * Get field value from entity
     *
     * @param array $field Field
     *
     * @return mixed
     */
    protected function getFieldEntityValue(array $field)
    {
        return $this->getEntity()->getItemNetPrice();
    }

    /**
     * Save field value to entity
     *
     * @param array $field Field
     * @param mixed $value Value
     *
     * @return void
     */
    protected function saveFieldEntityValue(array $field, $value)
    {
        // If entity is already in order (persitent) - just save itemNetPrice
        // else (entity is created) - set itemNetPrice to null to recalculate this value
        $this->getEntity()->setItemNetPrice($this->getEntity()->isPersistent() ? $value : null);
        $this->getEntity()->setPrice($value);
    }
}
