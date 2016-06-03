<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Menu\Admin\LeftMenu\Info;

/**
 * 'New product reviews' info panel notification class
 */
class NewReviews extends \XLite\View\Menu\Admin\LeftMenu\ANodeNotification
{
    /**
     * Maximum reviews count
     */
    const MAX_REVIEWS_COUNT = 100;

    /**
     * Cached value of counter
     *
     * @var integer
     */
    protected $reviewsCount;

    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array(
            'modules/XC/Reviews/style.css',
        );
    }

    /**
     * Check if data is updated (must be fast)
     *
     * @return boolean
     */
    public function isUpdated()
    {
        return $this->getLastReadTimestamp() < $this->getLastUpdateTimestamp();
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    public function getCacheParameters()
    {
        return array(
            'newReviewsUpdateTimestamp' => $this->getLastUpdateTimestamp(),
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->getCounter();
    }

    /**
     * Return update timestamp
     *
     * @return integer
     */
    protected function getLastUpdateTimestamp()
    {
        $result = \XLite\Core\TmpVars::getInstance()->newReviewsUpdateTimestamp;

        if (null === $result) {
            $result = LC_START_TIME;
            \XLite\Core\TmpVars::getInstance()->newReviewsUpdateTimestamp = $result;
        }

        return $result;
    }

    // {{{ View helpers

    /**
     * Returns node style class
     *
     * @return array
     */
    protected function getNodeStyleClasses()
    {
        $list = parent::getNodeStyleClasses();
        $list[] = 'new-reviews';

        return $list;
    }

    /**
     * Returns icon
     *
     * @return string
     */
    protected function getIcon()
    {
        return $this->getSVGImage('modules/XC/Reviews/images/icon-new-reviews.svg');
    }

    /**
     * Returns header url
     *
     * @return string
     */
    protected function getHeaderUrl()
    {
        return $this->buildURL('reviews');
    }

    /**
     * Returns header
     *
     * @return string
     */
    protected function getHeader()
    {
        return static::t('New product reviews');
    }

    /**
     * Get entries count
     *
     * @return string
     */
    protected function getCounter()
    {
        if (null === $this->reviewsCount) {
            $this->reviewsCount = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->search(
                $this->getReviewsSearchParams(),
                true
            );
        }

        return static::MAX_REVIEWS_COUNT <= $this->reviewsCount
            ? (static::MAX_REVIEWS_COUNT - 1) . '+'
            : $this->reviewsCount;
    }

    /**
     * Get parameters to search product reviews
     *
     * @return array
     */
    protected function getReviewsSearchParams()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_NEW} = 1;
        $cnd->{\XLite\Module\XC\Reviews\Model\Repo\Review::P_LIMIT} = array(0, static::MAX_REVIEWS_COUNT);

        return $cnd;
    }

    // }}}
}
