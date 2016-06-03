<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Customer\ProductInfo\ItemsList;

use XLite\Core\Auth;
use XLite\Core\Database;
use XLite\Core\View\DynamicWidgetInterface;
use XLite\Model\WidgetParam\TypeInt;
use XLite\View\CacheableTrait;

/**
 * Reviews list widget
 */
class AverageRating extends \XLite\Module\XC\Reviews\View\AverageRating implements DynamicWidgetInterface
{
    use CacheableTrait;

    /**
     * Widget parameters
     */
    const PARAM_PRODUCT_ID = 'productId';

    /**
     * @var \XLite\Model\Product
     */
    protected $product;

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            self::PARAM_PRODUCT_ID => new TypeInt('ProductId'),
        );
    }

    /**
     * Get associated product's id.
     *
     * @return int
     */
    protected function getProductId()
    {
        return $this->getParam(self::PARAM_PRODUCT_ID);
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        if (!isset($this->product)) {
            $this->product = Database::getRepo('XLite\Model\Product')->find($this->getProductId());

            $this->setWidgetParams([self::PARAM_PRODUCT => $this->product]);
        }

        return $this->product;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Reviews/product/items_list/rating.twig';
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $list = parent::getCacheParameters();

        $list[] = $this->getProductId();

        $list[] = Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->getVersion();

        $profile = Auth::getInstance()->getProfile();

        $list[] = $profile !== null ? $profile->getProfileId() : 0;

        return $list;
    }
}
