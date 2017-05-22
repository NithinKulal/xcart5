<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace XLite\Module\XC\CustomerAttachments\View\Product;

/**
 * Product list item widget
 */
class ListItem extends \XLite\View\Product\ListItem implements \XLite\Base\IDecorator
{
    /**
     * Return class attribute for the product cell
     *
     * @return object
     */
    public function getProductCellClass()
    {
        $class = parent::getProductCellClass();

        $class .= $this->getProduct()->isCustomerAttachmentsMandatory() ? ' attachment-required' : '';

        return $this->getSafeValue($class);
    }

    /**
     * Link should redirect to product page instead of adding to cart if attachment is required
     *
     * @return boolean
     */
    protected function isGotoProduct()
    {
        return parent::isGotoProduct()
            || $this->getProduct()->isCustomerAttachmentsMandatory();
    }
}
