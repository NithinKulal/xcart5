<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Controller\Admin;


/**
 * Products list controller
 */
class ProductList extends \XLite\Controller\Admin\ProductList implements \XLite\Base\IDecorator
{
    /**
     * Do action clone
     *
     * @return void
     */
    protected function doActionSaleCancelSale()
    {
        $select = \XLite\Core\Request::getInstance()->select;
        if ($select && is_array($select)) {
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById($this->getUpdateInfo());
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );
        } else {
           \XLite\Core\TopMessage::addWarning('Please select the products first');
        }
    }

    /**
     * Defines the update information
     * 
     * @return array
     */
    protected function getUpdateInfo()
    {
        return array_fill_keys(
            array_keys($this->getSelected()), 
            array('participateSale' => false)
        );
    }
}
