<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Sale\Controller\Admin;

/**
 * Sale selected controller
 */
class SaleSelected extends \XLite\Controller\Admin\AAdmin
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Set the sale price');
    }

    /**
     * Set sale price parameters for products list
     *
     * @return void
     */
    protected function doActionSetSalePrice()
    {
        $form = new \XLite\Module\CDev\Sale\View\Form\SaleSelectedDialog();
        $form->getRequestData();

        if ($form->getValidationMessage()) {
            \XLite\Core\TopMessage::addError($form->getValidationMessage());
        } else {
            \XLite\Core\Database::getRepo('\XLite\Model\Product')->updateInBatchById($this->getUpdateInfo());
            \XLite\Core\TopMessage::addInfo(
                'Products information has been successfully updated'
            );
        }

        $this->setReturnURL($this->buildURL('product_list', '', array('mode' => 'search')));
    }

    /**
     * Return result array to update in batch list of products
     *
     * @return array
     */
    protected function getUpdateInfo()
    {
        return array_fill_keys(
            array_keys($this->getSelected()),
            $this->getUpdateInfoElement()
        );
    }

    /**
     * Return one element to update.
     *
     * @return array
     */
    protected function getUpdateInfoElement()
    {
        $data = $this->getPostedData();

        return array(
            'participateSale' => (0 !== $data['salePriceValue'] || \XLite\Model\Product::SALE_DISCOUNT_TYPE_PERCENT !== $data['discountType'])
        ) + $data;
    }
}
