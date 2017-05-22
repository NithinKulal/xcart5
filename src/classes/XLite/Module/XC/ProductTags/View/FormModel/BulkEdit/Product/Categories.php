<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\ProductTags\View\FormModel\BulkEdit\Product;

/**
 * @Decorator\Depend ("XC\BulkEditing")
 */
class Categories extends \XLite\Module\XC\BulkEditing\View\FormModel\Product\Categories implements \XLite\Base\IDecorator
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ProductTags/form_model/bulk_edit/product.css';

        return $list;
    }
}
