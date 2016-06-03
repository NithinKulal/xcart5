<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\PINCodes\View\FormField\Inline\Input\Text\Integer;

/**
 * Product quantity
 *
 */
class ProductQuantity extends \XLite\View\FormField\Inline\Input\Text\Integer\ProductQuantity implements \XLite\Base\IDecorator
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/CDev/PINCodes/products_list/style.css';

        return $list;
    }

    /**
     * Is editable
     *
     * @return boolean
     */
    public function isEditable()
    {
        return parent::isEditable() && !$this->getEntity()->hasManualPinCodes();
    }

    /**
     * Get view template
     *
     * @return void
     */
    protected function getViewTemplate()
    {
        return $this->getEntity()->hasManualPinCodes()
            ? 'modules/CDev/PINCodes/products_list/product_quantity.twig'
            : 'form_field/inline/input/text/integer/product_quantity.twig';
    }

    /**
     * getPinCodesManagementPageUrl
     *
     * @return string
     */
    protected function getPinCodesManagementPageUrl()
    {
        return $this->buildUrl(
            'product',
            '',
            array('product_id' => $this->getEntity()->getId(), 'page' => 'pin_codes')
        );
    }
}

