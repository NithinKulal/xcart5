<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Taxes\Inline;

/**
 * Shipping tax class
 */
class ShippingTaxClass extends \XLite\View\FormField\Inline\Base\Single
{
    /**
     * Define form field
     *
     * @return string
     */
    protected function defineFieldClass()
    {
        return 'XLite\View\Taxes\TaxClass';
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
     * Save value
     *
     * @return void
     */
    public function saveValue()
    {
        $value = $this->getSingleFieldAsWidget()->getValue();
        $entity = $this->getEntity();
        if ($value === "-1") {
            $entity->setTaxClass(null);

        } else {
            $entity->setTaxClass(\XLite\Core\Database::getRepo('XLite\Model\TaxClass')->find($value));
        }
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
        return !$this->getEntity()->getTaxClass()
            ? -1
            : parent::getFieldEntityValue($field);
    }
}
