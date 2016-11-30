<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\ProductAdvisor\View;

/**
 * 'Customers who bought this product also bought' widget
 *
 * @ListChild (list="center.bottom", zone="customer", weight="700")
 */
class BoughtBought extends \XLite\Module\CDev\ProductAdvisor\View\ABought
{
    /**
     * Return search parameters.
     *
     * @return array
     */
    public static function getSearchParams()
    {
        return [
            \XLite\Model\Repo\Product::P_EXCL_PRODUCT_ID => self::PARAM_PRODUCT_ID,
        ];
    }

    /**
     * Runtime cache for profile ids
     *
     * @var array
     */
    protected $profileIds = [];

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' bought-bought-products';
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return static::t('Customers who bought this product also bought');
    }

    /**
     * Get max count of items
     *
     * @return integer
     */
    protected function getMaxCount()
    {
        return (int) \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cbb_max_count_in_block;
    }

    /**
     * Returns true if block is enabled
     *
     * @return boolean
     */
    protected function isBlockEnabled()
    {
        return \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cbb_enabled;
    }

    /**
     * Default search conditions
     *
     * @param  \XLite\Core\CommonCell $searchCase Search case
     *
     * @return \XLite\Core\CommonCell
     */
    protected function postprocessSearchCase(\XLite\Core\CommonCell $searchCase)
    {
        $searchCase = parent::postprocessSearchCase($searchCase);

        $productId = $this->getParam(self::PARAM_PRODUCT_ID);

        $searchCase->{\XLite\Module\CDev\ProductAdvisor\Model\Repo\Product::P_PROFILE_ID}
                                                                    = $this->getProfilesIds($productId);
        $searchCase->{\XLite\Model\Repo\Product::P_EXCL_PRODUCT_ID} = $productId;

        return $searchCase;
    }

    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getLimitCondition()
    {
        $cnd = $this->getSearchCondition();
        if (!$this->getParam(\XLite\View\Pager\APager::PARAM_SHOW_ITEMS_PER_PAGE_SELECTOR)) {
            return $this->getPager()->getLimitCondition(
                0,
                $this->getParam(\XLite\View\Pager\APager::PARAM_MAX_ITEMS_COUNT),
                $cnd
            );
        }

        return $cnd;
    }

    /**
     * Return 'Order by' array.
     * array(<Field to order>, <Sort direction>)
     *
     * @return array|null
     */
    protected function getOrderBy()
    {
        return ['cnt', static::SORT_ORDER_DESC];
    }

    /**
     * Get array of profiles IDs of users who bought product with specified product ID
     *
     * @param integer $productId Product ID
     *
     * @return array
     */
    protected function getProfilesIds($productId)
    {
        return $this->executeCachedRuntime(
            function () use ($productId) {
                return \XLite\Core\Database::getRepo('XLite\Model\Order')->findUsersBoughtProduct($productId);
            },
            ['getProfilesIds', $productId]
        );
    }
}
