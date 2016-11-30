<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Upselling\Controller\Admin;

/**
 * Upselling products
 */
class UpsellingProducts extends \XLite\Controller\Admin\AAdmin
{
    /**
     * The parent product ID definition
     *
     * @return string
     */
    public function getParentProductId()
    {
        return \XLite\Core\Request::getInstance()->product_id ?: \XLite\Core\Request::getInstance()->id;
    }

    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('Related products page');
    }

    /**
     * Get upselling products list
     *
     * @return array(\XLite\Module\XC\Upselling\Model\UpsellingProduct) Objects
     */
    public function getUpsellingList()
    {
        return \XLite\Core\Database::getRepo('\XLite\Module\XC\Upselling\Model\UpsellingProduct')
            ->getUpsellingProducts(\XLite\Core\Request::getInstance()->parent_product_id);
    }

    /**
     * doActionAddUpselling
     *
     * @return void
     */
    protected function doActionAdd()
    {
        if (is_array(\XLite\Core\Request::getInstance()->select)) {
            $pids = array_keys(\XLite\Core\Request::getInstance()->select);
            $products = \XLite\Core\Database::getRepo('\XLite\Model\Product')
                ->findByIds($pids);

            $this->id = \XLite\Core\Request::getInstance()->product_id;
            $parentProduct = \XLite\Core\Database::getRepo('\XLite\Model\Product')->find($this->id);

            $existingLinksIds = array();
            $existingLinks = $this->getUpsellingList();

            if ($existingLinks) {
                foreach ($existingLinks as $k => $v) {
                    $existingLinksIds[] = $v->getProduct()->getProductId();
                }
            }

            if ($products) {
                foreach ($products as $product) {
                    if (in_array($product->getProductId(), $existingLinksIds)) {
                        \XLite\Core\TopMessage::addWarning(
                            'The product SKU#"X" is already set as Related for the product',
                            array('SKU' => $product->getSku())
                        );
                    } else {
                        $up = new \XLite\Module\XC\Upselling\Model\UpsellingProduct();
                        $up->setProduct($product);
                        $up->setParentProduct($parentProduct);

                        \XLite\Core\Database::getEM()->persist($up);
                        \XLite\Core\Database::getEM()->flush($up);

                        if (\XLite\Core\Request::getInstance()->mutualRelations) {
                            \XLite\Core\Database::getRepo('XLite\Module\XC\Upselling\Model\UpsellingProduct')
                                ->addBidirectionalLink($up);
                        }
                    }
                }
            }
        }

        $this->setReturnURL(
            $this->buildURL(
                'product',
                '',
                array(
                    'page'       => 'upselling_products',
                    'product_id' => $this->id,
                )
            )
        );
        $this->setHardRedirect(true);
    }

    /**
     * Delete upselling links from product
     *
     * @return void
     */
    protected function doActionDelete()
    {
        $links = \XLite\Core\Database::getRepo('\XLite\Module\XC\Upselling\Model\UpsellingProduct')
            ->getUpsellingProducts($this->getParentProductId());

        foreach ($links as $link) {
            \XLite\Core\Database::getEM()->remove($link);
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Delete upselling links from product
     *
     * @return void
     */
    protected function doActionEnableMutual()
    {
        $links = \XLite\Core\Database::getRepo('\XLite\Module\XC\Upselling\Model\UpsellingProduct')
            ->getUpsellingProducts($this->getParentProductId());

        foreach ($links as $link) {
            \XLite\Core\Database::getRepo('XLite\Module\XC\Upselling\Model\UpsellingProduct')
                ->addBidirectionalLink($link);
        }

        \XLite\Core\Database::getEM()->flush();
    }

    /**
     * Delete upselling links from product
     *
     * @return void
     */
    protected function doActionDisableMutual()
    {
        $links = \XLite\Core\Database::getRepo('\XLite\Module\XC\Upselling\Model\UpsellingProduct')
            ->getUpsellingProducts($this->getParentProductId());

        foreach ($links as $link) {
            \XLite\Core\Database::getRepo('XLite\Module\XC\Upselling\Model\UpsellingProduct')
                ->deleteBidirectionalLink($link);
        }

        \XLite\Core\Database::getEM()->flush();
    }
}
