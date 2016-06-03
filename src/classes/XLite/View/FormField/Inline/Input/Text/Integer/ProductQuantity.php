<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Integer;

/**
 * Product quantity
 */
class ProductQuantity extends \XLite\View\FormField\Inline\Input\Text\Integer
{
    /**
     * Save value
     *
     * @return void
     */
    public function saveValue()
    {
        $this->getEntity()->setAmount($this->getSingleFieldAsWidget()->getValue());
    }

    /**
     * Get entity value for field
     *
     * @return mixed
     */
    protected function getEntityValue()
    {
        return $this->getEntity()->getPublicAmount();
    }

    /**
     * Check - field is editable or not
     *
     * @return boolean
     */
    protected function isEditable()
    {
        return parent::isEditable() && $this->getEntity()->getInventoryEnabled();
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

    /**
     * Get view template
     *
     * @return void
     */
    protected function getViewTemplate()
    {
        return 'form_field/inline/input/text/integer/product_quantity.twig';
    }

}

