<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Inline\Input\Text\Product;

/**
 * Product SKU
 */
class SKU extends \XLite\View\FormField\Inline\Input\Text
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'form_field/inline/input/text/product/sku.css';

        return $list;
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
        return parent::getFieldParams($field) + array('maxlength' => 32);
    }

    /**
     * Validate SKU 
     *
     * @param array $field Feild info
     *
     * @return array
     */
    protected function validateSku(array $field)
    {
        $result = array(true, null);
        try {
            $product = $this->getEntity();
            $validator = new \XLite\Core\Validator\SKU($product ? $product->getProductId() : null);
            $validator->validate($field['widget']->getValue());
        } catch (\Exception $e) {
            $result = array(
                false,
                $e->getMessage()
            );
        }

        return $result;
    }
}
