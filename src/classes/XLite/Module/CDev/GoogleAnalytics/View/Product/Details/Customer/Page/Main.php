<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\View\Product\Details\Customer\Page;

use XLite\Module\CDev\GoogleAnalytics\Logic\DataMapper\ProductDataMapper;

/**
 * Main
 */
class Main extends \XLite\View\Product\Details\Customer\Page\Main implements \XLite\Base\IDecorator
{
    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        $list = parent::getContainerAttributes();

        if ($this->getProduct()
            && \XLite\Module\CDev\GoogleAnalytics\Main::isECommerceEnabled()
        ) {
            \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);

            $categoryName   = $this->getCategoryName();
            $coupon         = ''; // In case we will introduce per-product coupons
            $position       = '';

            $list['data-ga-ec-action'] = json_encode(
                [
                    'ga-type'   => 'addProduct',
                    'ga-action' => 'detail',
                    'data'      => ProductDataMapper::getAddProductData(
                        $this->getProduct(),
                        $categoryName,
                        $coupon,
                        $position
                    ),
                ],
                JSON_FORCE_OBJECT
            );

            \XLite\Core\Translation::setTmpTranslationCode(null);
        }

        return $list;
    }

    /**
     * @return string
     */
    protected function getCategoryName()
    {
        $categoryName   = '';

        $category = method_exists(\XLite::getController(), 'getCategory') || method_exists($this, 'getCategory')
            ? $this->getCategory()
            : $this->getProduct()->getCategory();

        if ($category) {
            $categoryName = $category
                ? $category->getName()
                : '';
        }

        return $categoryName;
    }
}
