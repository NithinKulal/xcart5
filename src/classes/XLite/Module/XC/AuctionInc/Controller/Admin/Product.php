<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\AuctionInc\Controller\Admin;

/**
 * Product
 */
class Product extends \XLite\Controller\Admin\Product implements \XLite\Base\IDecorator
{
    /**
     * Get pages sections
     *
     * @return array
     */
    public function getPages()
    {
        $list = parent::getPages();
        if (!$this->isNew()) {
            $list['auctionInc'] = static::t('ShippingCalc');
        }

        return $list;
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        if (!$this->isNew()) {
            $list['auctionInc'] = 'modules/XC/AuctionInc/product.twig';
        }

        return $list;
    }

    /**
     * Return model form object
     *
     * @param array $params Form constructor params OPTIONAL
     *
     * @return \XLite\View\Model\AModel
     */
    protected function getAuctionIncModelForm(array $params = array())
    {
        $class = 'XLite\Module\XC\AuctionInc\View\Model\ProductAuctionInc';

        return \XLite\Model\CachingFactory::getObject(
            __METHOD__ . $class . (empty($params) ? '' : md5(serialize($params))),
            $class,
            $params
        );
    }

    /**
     * Update AuctionInc related data
     *
     * @return void
     */
    protected function doActionUpdateAuctionInc()
    {
        $this->getAuctionIncModelForm()->performAction('modify');
    }

    /**
     * Update AuctionInc related data
     *
     * @return void
     */
    protected function doActionRestoreAuctionInc()
    {
        /** @var \XLite\Module\XC\AuctionInc\Model\ProductAuctionInc $auctionIncData */
        $auctionIncData = $this->getProduct()->getAuctionIncData();

        if ($auctionIncData && $auctionIncData->isPersistent()) {
            $auctionIncData->delete();
        }

        $this->setReturnURL(
            $this->buildURL(
                'product',
                null,
                array(
                    'product_id' => $this->getProductId(),
                    'page' => 'auctionInc',
                )
            )
        );
    }
}
