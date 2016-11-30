<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Controller\Customer;

/**
 * Reviews controller
 *
 */
class ProductReviews extends \XLite\Controller\Customer\Product
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getProduct()
            ? static::t('Ratings and Reviews of X', array('product' => $this->getProduct()->getName()))
            : null;
    }

    /**
     * Return the current page title (for the <title> tag)
     *
     * @return string
     */
    public function getTitleObjectPart()
    {
        return $this->getProduct() && $this->getProduct()->getMetaTitle()
            ? static::t('Ratings and Reviews of X', array('product' => $this->getProduct()->getMetaTitle()))
            : $this->getTitle();
    }


    /**
     * Check whether the category title is visible in the content area
     *
     * @return boolean
     */
    public function isTitleVisible()
    {
        return false;
    }

    /**
     * Return product id of the current page
     *
     * @return integer
     */
    public function getProductId()
    {
        $productId = parent::getProductId();
        if (empty($productId)) {
            $cellName = \XLite\Module\XC\Reviews\View\ItemsList\Model\Customer\Review::getSessionCellName();
            $cell = (array)\XLite\Core\Session::getInstance()->$cellName;

            $productId = $cell['product_id'];
        }

        return $productId;
    }

    /**
     * Return category id of current page
     *
     * @return integer
     */
    public function getCategoryId()
    {
        $categoryId = parent::getCategoryId();
        if (empty($categoryId)) {
            $cellName = \XLite\Module\XC\Reviews\View\ItemsList\Model\Customer\Review::getSessionCellName();
            $cell = (array)\XLite\Core\Session::getInstance()->$cellName;

            $categoryId = isset($cell['category_id'])
                ? $cell['category_id']
                : $this->getProduct()->getCategoryId();
        }

        return $categoryId;
    }

    /**
     * Get meta description
     *
     * @return string
     */
    public function getMetaDescription()
    {
        $review = $this->getReviewForMeta();

        return $review
            ? $review->getMetaDescription()
            : parent::getMetaDescription();
    }

    /**
     * Get meta keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        $keywords = array();

        if ($this->isVisible()) {
            $keywords = array(
                $this->getProduct()->getName(),
                'Ratings',
                'Reviews',
                \Xlite\Core\Config::getInstance()->Company->company_name,
            );
        }

        return $keywords
            ? implode(', ', $keywords)
            : parent::getKeywords();
    }

    // {{{ Search

    /**
     * Get search condition parameter by name
     *
     * @param string $paramName Parameter name
     *
     * @return mixed
     */
    public function getCondition($paramName)
    {
        $searchParams = $this->getConditions();

        return isset($searchParams[$paramName])
            ? $searchParams[$paramName]
            : null;
    }

    /**
     * Get date condition parameter (start or end)
     *
     * @param boolean $start Start date flag, otherwise - end date  OPTIONAL
     *
     * @return mixed
     */
    public function getDateCondition($start = true)
    {
        $dates = $this->getCondition(\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_ADDITION_DATE);
        $n = (true === $start) ? 0 : 1;

        $date = isset($dates[$n]) ? $dates[$n] : LC_START_TIME;
        if ($start && LC_START_TIME == $date) {
            $date -= 86400 * 30;
        }

        return $date;
    }

    /**
     * Get date value for search params
     *
     * @param string  $fieldName Field name (prefix)
     * @param boolean $isEndDate End date flag OPTIONAL
     *
     * @return integer
     */
    public function getDateValue($fieldName, $isEndDate = false)
    {
        $dateValue = \XLite\Core\Request::getInstance()->$fieldName;

        if (!isset($dateValue)) {
            $nameDay   = $fieldName . 'Day';
            $nameMonth = $fieldName . 'Month';
            $nameYear  = $fieldName . 'Year';

            if (isset(\XLite\Core\Request::getInstance()->$nameMonth)
                && isset(\XLite\Core\Request::getInstance()->$nameDay)
                && isset(\XLite\Core\Request::getInstance()->$nameYear)
            ) {
                $dateValue = mktime(
                    $isEndDate ? 23 : 0,
                    $isEndDate ? 59 : 0,
                    $isEndDate ? 59 : 0,
                    \XLite\Core\Request::getInstance()->$nameMonth,
                    \XLite\Core\Request::getInstance()->$nameDay,
                    \XLite\Core\Request::getInstance()->$nameYear
                );
            }
        }

        return $dateValue;
    }

    /**
     * Save search conditions
     *
     * @return void
     */
    protected function doActionSearch()
    {
        $cellName = \XLite\Module\XC\Reviews\View\ItemsList\Model\Review::getSessionCellName();

        \XLite\Core\Session::getInstance()->$cellName = $this->getSearchParams();
    }

    /**
     * Return search parameters
     *
     * @return array
     */
    protected function getSearchParams()
    {
        // Prepare dates

        $this->startDate = $this->getDateValue('startDate');
        $this->endDate   = $this->getDateValue('endDate', true);

        if (0 === $this->startDate
            || 0 === $this->endDate
            || $this->startDate > $this->endDate
        ) {
            $date = getdate(time());

            $this->startDate = mktime(0, 0, 0, $date['mon'], 1, $date['year']);
            $this->endDate   = mktime(0, 0, 0, $date['mon'], $date['mday'], $date['year']);
        }

        $searchParams = $this->getConditions();

        foreach (\XLite\Module\XC\Reviews\View\ItemsList\Model\Review::getSearchParams() as $requestParam) {
            if (\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_ADDITION_DATE === $requestParam) {
                $searchParams[$requestParam] = array($this->startDate, $this->endDate);
            } elseif (isset(\XLite\Core\Request::getInstance()->$requestParam)) {
                $searchParams[$requestParam] = \XLite\Core\Request::getInstance()->$requestParam;
            }
        }

        return $searchParams;
    }

    /**
     * Get search conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cellName = \XLite\Module\XC\Reviews\View\ItemsList\Model\Review::getSessionCellName();

        $searchParams = \XLite\Core\Session::getInstance()->$cellName;

        if (!is_array($searchParams)) {
            $searchParams = array();
        }

        return $searchParams;
    }

    // }}}

    /**
     * Update list
     *
     * @return void
     */
    protected function doActionUpdate()
    {
        $list = new \XLite\Module\XC\Reviews\View\ItemsList\Model\Review;
        $list->processQuick();
    }

    /**
     * Get pages templates
     *
     * @return array
     */
    protected function getPageTemplates()
    {
        $list = parent::getPageTemplates();
        $list['default'] = 'modules/XC/Reviews/reviews_page/list.twig';

        return $list;
    }

    /**
     * Common method to determine current location
     *
     * @return string
     */
    protected function getLocation()
    {
        return $this->getTitle();
    }

    /**
     * Add part to the location nodes list
     *
     * @return void
     */
    protected function addBaseLocation()
    {
        parent::addBaseLocation();

        if ($this->getProduct()) {
            $this->addLocationNode(
                $this->getProduct()->getName(),
                $this->buildURL('product', '', array('product_id' => $this->getProductId()))
            );
        }
    }

    /**
     * Check controller visibility
     *
     * @return boolean
     */
    protected function isVisible()
    {
        $product = $this->getProduct();

        return parent::isVisible()
            && $product
            && $product->isVisible()
            && (0 < $product->getReviewsCount() || $this->isProductReviewedByUser());
    }

    /**
     * Check if redirect to clean URL is needed
     *
     * @return boolean
     */
    protected function isRedirectToCleanURLNeeded()
    {
        return false;
    }

    /**
     * Defines the common data for JS
     *
     * @return array
     */
    public function defineCommonJSData()
    {
        return array_merge(
            parent::defineCommonJSData(),
            array(
                'product_id'    => $this->getProductId(),
                'category_id'   => $this->getCategoryId(),
            )
        );
    }

    /**
     * Returns review for meta
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    protected function getReviewForMeta()
    {
        $product = $this->getProduct();
        /** @var \XLite\Module\XC\Reviews\Model\Repo\Review $repo */
        $repo = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review');

        return $repo->findOneForMeta($product);
    }
}
