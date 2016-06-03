<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View;

/**
 * Average product rating widget
 */
class AverageRating extends \XLite\View\AView
{
    /**
     * Widget params names
     */
    const PARAM_PRODUCT     = 'product';
    const PARAM_WIDGET_MODE = 'widgetMode';

    /**
     * Ratings distortion
     *
     * @var array
     */
    protected $ratings;

    /**
     * Average product rating
     *
     * @var integer
     */
    protected $averageRating;

    /**
     * Reviews count for product
     *
     * @var integer
     */
    protected $reviewsCount;

    /**
     * Votes count for product
     *
     * @var integer
     */
    protected $votesCount;

    /**
     * Maximum available product rating
     *
     * @var integer
     */
    protected $maxRatingValue;

    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Reviews/average_rating/style.css';
        $list[] = 'modules/XC/Reviews/vote_bar/vote_bar.css';
        $list[] = 'modules/XC/Reviews/form_field/input/rating/rating.css';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/Reviews/average_rating/rating.js';

        return $list;
    }

    /**
     * Return TRUE if customer can rate product
     *
     * @return boolean
     */
    public function isAllowedRateProduct()
    {
        $result = \XLite\Core\Auth::getInstance()->getProfile()
            ? true
            : false;

        if ($result && $this->isProductRatedByUser()) {
            $result = false;
        }

        if ($result && $this->isPurchasedCustomerOnlyAbleLeaveFeedback()
            && \XLite\Core\Auth::getInstance()->getProfile()
            && !$this->isUserPurchasedProduct()
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Return product ID
     *
     * @return integer
     */
    public function getRatedProductId()
    {
        $product = $this->getRatedProduct();

        return $product ? $product->getProductId() : null;
    }

    /**
     * Return TRUE if customer already rated product
     *
     * @return boolean
     */
    public function isProductRatedByUser()
    {
        return $this->getRatedProduct()->isRatedByUser(\XLite\Core\Auth::getInstance()->getProfile());
    }

    /**
     * Return true if customer purchased the specified product
     *
     * @param integer $productId Product ID
     *
     * @return boolean
     */
    protected function isUserPurchasedProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\OrderItem')
            ->countItemsPurchasedByCustomer($this->getRatedProductId(), \XLite\Core\Auth::getInstance()->getProfile());
    }

    /**
     * Define if the current page is detailed product page
     *
     * @return boolean
     */
    public function isDetailedProductInfo()
    {
        return ('product' == \XLite\Core\Request::getInstance()->target);
    }

    /**
     * Return ratings distortion
     *
     * @return array
     */
    public function getRatings()
    {
        if (!isset($this->ratings)) {
            $this->ratings = $this->getRatedProduct()->getRatings();
        }

        return $this->ratings;
    }

    /**
     * Return average rating for the current product
     *
     * @return integer
     */
    public function getAverageRating()
    {
        if (!isset($this->averageRating)) {
            $this->averageRating = $this->getRatedProduct()->getAverageRating();
        }

        return $this->averageRating;
    }

    /**
     * Return reviews count for the current product
     *
     * @return integer
     */
    public function getReviewsCount()
    {
        if (!isset($this->reviewsCount)) {
            $this->reviewsCount = $this->getRatedProduct()->getReviewsCount();
        }

        return $this->reviewsCount;
    }

    /**
     * Return votes count for the current product
     *
     * @return integer
     */
    public function getVotesCount()
    {
        if (!isset($this->votesCount)) {
            $this->votesCount = $this->getRatedProduct()->getVotesCount();
        }

        return $this->votesCount;
    }

    /**
     * Return max available rating value
     *
     * @return integer
     */
    public function getMaxRatingValue()
    {
        if (!isset($this->maxRatingValue)) {
            $this->maxRatingValue = $this->getRatedProduct()->getMaxRatingValue();
        }

        return $this->maxRatingValue;
    }

    /**
     * Define whether product was rated somewhere or not
     *
     * @return boolean
     */
    public function isVisibleAverageRating()
    {
        return 0 < $this->getAverageRating();
    }

    /**
     * Define whether to display the rating on the page
     *
     * @return boolean
     */
    public function isVisibleAverageRatingOnPage()
    {
        return true;
    }

    /**
     * Define whether to display the rating on the page
     *
     * @return boolean
     */
    public function isVisibleReviewsCount()
    {
        return 0 < $this->getReviewsCount()
            || 'grid' != $this->getWidgetMode();
    }

    /**
     * Get widget mode
     *
     * @return string
     */
    protected function getWidgetMode()
    {
        return $this->getParam(static::PARAM_WIDGET_MODE);
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += array(
            static::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null, false, 'XLite\Model\Product'),
            static::PARAM_WIDGET_MODE => new \XLite\Model\WidgetParam\TypeString('Widget mode', 'product-details'),
        );
    }

    /**
     * Return title
     *
     * @return string
     */
    protected function getHead()
    {
        return null;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/Reviews';
    }

    /**
     * Return default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Reviews/average_rating/rating.twig';
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(static::PARAM_PRODUCT);
    }

    /**
     * Return product
     *
     * @return \XLite\Model\Product
     */
    protected function getRatedProduct()
    {
        return $this->getProduct() ?:
            \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId());
    }

    /**
     * Return product URL
     *
     * @return string
     */
    public function getRatedProductURL()
    {
        $product = $this->getRatedProduct();

        return $product
            ? $this->buildURL('product', null, array('product_id' => $product->getId(), 'category_id' => $product->getCategoryId())) . '#product-details-tab-reviews'
            : '';
    }
}
