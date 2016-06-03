<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\Logic\Import\Processor;

/**
 * Products
 */
class Products extends \XLite\Logic\Import\Processor\Products implements \XLite\Base\IDecorator
{
    /**
     * Define columns
     *
     * @return array
     */
    protected function defineColumns()
    {
        $columns = parent::defineColumns();

        $columns += array(
            'relatedProducts' => array(
                static::COLUMN_IS_MULTIPLE => true
            ),
        );

        return $columns;
    }

    // }}}

    // {{{ Verification

    /**
     * Get messages
     *
     * @return array
     */
    public static function getMessages()
    {
        return parent::getMessages()
            + array(
                'RELATED-PRODUCT-SKU-FMT' => 'Product with SKU "{{value}}" does not exist and relation will not be created',
            );
    }

    /**
     * Verify 'SKU' value
     *
     * @param mixed $value  Value
     * @param array $column Column info
     *
     * @return void
     */
    protected function verifyRelatedProducts($value, array $column)
    {
        if (!$this->verifyValueAsEmpty($value)) {

            foreach ($value as $relProductSku) {
                if (!$this->verifyValueAsEmpty($relProductSku) && !$this->verifyValueAsProduct($relProductSku)) {
                    $this->addWarning('RELATED-PRODUCT-SKU-FMT', array('column' => $column, 'value' => $relProductSku));
                }
            }
        }
    }

    // }}}

    // {{{ Import

    /**
     * Import 'relatedProducts' value
     *
     * @param \XLite\Model\Product $model  Product
     * @param array                $value  Value (array of related products SKUs)
     * @param array                $column Column info
     *
     * @return void
     */
    protected function importRelatedProductsColumn(\XLite\Model\Product $model, array $value, array $column)
    {
        $currentRelations = \XLite\Core\Database::getRepo('XLite\Module\XC\Upselling\Model\UpsellingProduct')
            ->getUpsellingProducts($model->getProductId());

        $relSku = array();

        if ($currentRelations) {

            // Get SKU cache of currently assigned related products
            // and remove related products which are not in new values list

            $toDelete = array();

            foreach ($currentRelations as $rel) {
                $relSku[] = $rel->getProduct()->getSku();
                if ($rel->getProduct() && !in_array($rel->getProduct()->getSku(), $value)) {
                    $toDelete[] = $rel;
                }
            }

            if ($toDelete) {
                \XLite\Core\Database::getRepo('XLite\Module\XC\Upselling\Model\UpsellingProduct')
                    ->deleteInBatch($toDelete);
            }
        }

        if ($value) {

            // Add current product SKU to avoid creations of the related product with the same SKU
            $relSku[] = $model->getSku();

            foreach ($value as $relProductSku) {

                if (!in_array($relProductSku, $relSku)) {

                    // Create new relation

                    $relProduct = \XLite\Core\Database::getRepo('XLite\Model\Product')->findOneBySku($relProductSku);

                    if ($relProduct) {
                        $up = new \XLite\Module\XC\Upselling\Model\UpsellingProduct();
                        $up->setProduct($relProduct);
                        $up->setParentProduct($model);

                        \XLite\Core\Database::getEM()->persist($up);
                    }
                }
            }
        }
    }

    // }}}
}
