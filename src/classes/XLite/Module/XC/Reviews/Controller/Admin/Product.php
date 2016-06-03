<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Controller\Admin;

/**
 * Product modify controller
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
            $list['product_reviews'] = static::t('Product reviews');
        }

        return $list;
    }

    /**
     * Handles the request
     *
     * @return void
     */
    public function handleRequest()
    {
        $cellName = \XLite\Module\XC\Reviews\View\ItemsList\Model\Review::getSessionCellName();
        \XLite\Core\Session::getInstance()->$cellName = array(
            \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_PRODUCT => $this->getProductId(),
        );

        parent::handleRequest();
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
            $list['product_reviews'] = 'modules/XC/Reviews/product/reviews.twig';
        }

        return $list;
    }
}
