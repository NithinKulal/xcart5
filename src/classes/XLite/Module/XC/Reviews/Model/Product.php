<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Model;

/**
 * Product
 */
class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Product reviews
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @OneToMany (targetEntity="XLite\Module\XC\Reviews\Model\Review", mappedBy="product", cascade={"all"})
     * @OrderBy   ({"additionDate" = "DESC"})
     */
    protected $reviews;

    /**
     * Review added by user (run-time cache)
     *
     * @var \XLite\Module\XC\Reviews\Model\Review
     */
    protected $reviewAddedByUser = null;

    /**
     * Votes count (run-time cache)
     *
     * @var integer
     */
    protected $votesCount = null;

    /**
     * Reviews count (run-time cache)
     *
     * @var integer
     */
    protected $reviewsCount = null;

    /**
     * Average rating (run-time cache)
     *
     * @var float
     */
    protected $averageRating = null;

    /**
     * Return count of votes
     *
     * @return integer
     */
    public function getVotesCount()
    {
        if (!isset($this->votesCount)) {
            $cnd = $this->getConditions();
            $countOnly = true;
            $this->votesCount = \XLite\Core\Database::getRepo('\XLite\Module\XC\Reviews\Model\Review')->search($cnd, $countOnly);
        }

        return $this->votesCount;
    }

    /**
     * Return product reviews count
     *
     * @return integer
     */
    public function getReviewsCount()
    {
        if (!isset($this->reviewsCount)) {
            $cnd = $this->getConditions();
            $cnd->{\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_TYPE}
                = \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_TYPE_REVIEWS_ONLY;
            $countOnly = true;
            $this->reviewsCount = \XLite\Core\Database::getRepo('\XLite\Module\XC\Reviews\Model\Review')->search($cnd, $countOnly);
        }

        return $this->reviewsCount;
    }

    /**
     * Return product average rating
     *
     * @return float
     */
    public function getAverageRating()
    {
        if (!isset($this->averageRating)) {
            $cnd = $this->getConditions();

            $avg = \XLite\Core\Database::getRepo('\XLite\Module\XC\Reviews\Model\Review')->search(
                $cnd,
                \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_MODE_AVG
            );

            $this->averageRating = $avg !== null
                ? number_format($avg, 2)
                : 0;
        }

        return $this->averageRating;
    }

    /**
     * Return ratings distortion
     *
     * @return array
     */
    public function getRatings()
    {
        $cnd = $this->getConditions();

        $maxRating = $this->getMaxRatingValue();
        $countOnly = true;

        $totalCount = $this->getVotesCount();

        $result = array();

        if (0 < $totalCount) {
            for ($rating = $maxRating; 0 < $rating; $rating--) {
                $cnd->{\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_RATING} = $rating;
                $count
                    = \XLite\Core\Database::getRepo('\XLite\Module\XC\Reviews\Model\Review')->search($cnd, $countOnly);

                $percent = ceil(100 * $count / $totalCount);
                $result[] = array(
                    'count'                 => $count,
                    'percent'               => $percent,
                    'rating'                => $rating,
                    'showPercentLastDiv'    => (98 > $percent),
                );
            }
        }

        return $result;
    }

    /**
     * Define whether product was rated somewhere or not
     *
     * @return boolean
     */
    public function isEmptyAverageRating()
    {
        return 0 >= $this->getAverageRating();
    }

    /**
     * Return maximum allowable rating value
     *
     * @return integer
     */
    public function getMaxRatingValue()
    {
        return \XLite\Module\XC\Reviews\Model\Review::MAX_RATING;
    }

    /**
     * Return review added by customer
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    public function getReviewAddedByUser(\XLite\Model\Profile $profile = null, $findByIp = true)
    {
        if (!isset($this->reviewAddedByUser)) {
            $this->reviewAddedByUser = $this->getReviewAddedByUserFromDb($profile);

            // If any customer has added review during getTtlLimitForReviewFromIp() time then
            // other customers cannot add reviews
            if (!$this->reviewAddedByUser) {
                $this->reviewAddedByUser = $this->findReviewByIp(
                    inet_pton($_SERVER['REMOTE_ADDR'])
                );
            }
        }

        return $this->reviewAddedByUser ?: null;
    }

    /**
     * Get review added by customer
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XLite\Module\XC\Reviews\Model\Review
     */
    protected function getReviewAddedByUserFromDb(\XLite\Model\Profile $profile = null)
    {
        $review = null;

        $data = array(
            'product' => $this,
        );

        if ($profile) {
            // Find by profile of logged in user
            $data['profile'] = $profile;
            $review = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->findOneBy($data);

        }

        return $review ?: false;
    }

    /**
     * Find reviews added by ip
     *
     * @param string $ip Ip to search
     *
     * @return \XLite\Module\XC\Reviews\Model\Review | null
     */
    protected function findReviewByIp($ip)
    {
        $data = array(
            'product'   => $this,
            'ip'        => $ip,
        );
        $reviews = \XLite\Core\Database::getRepo('XLite\Module\XC\Reviews\Model\Review')->findBy($data);

        $resultReview = null;
        foreach ($reviews as $tempReview) {
            if ($tempReview->getAdditionDate() >= (LC_START_TIME - $this->getTtlLimitForReviewFromIp())) {
                $resultReview = $tempReview;
                break;
            }
        }

        return $resultReview;
    }

    /**
     * Return TRUE if customer already rated product
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    public function isRatedByUser(\XLite\Model\Profile $profile = null)
    {
        $review = $this->getReviewAddedByUser($profile);

        return (null != $review);
    }

    /**
     * Return TRUE if customer already addded review for the product
     *
     * @return boolean
     */
    public function isReviewedByUser(\XLite\Model\Profile $profile = null)
    {
        $review = $this->getReviewAddedByUser($profile);

        return (null != $review && $review->getReview());
    }

    /**
     * Get time to live for limiting adding reviews from one ip for certain product
     *
     * @return integer
     */
    protected function getTtlLimitForReviewFromIp()
    {
        return \XLite\Module\XC\Reviews\Model\Review::TTL_LIMIT_FOR_REVIEW_FROM_IP;
    }

    /**
     * Get conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_PRODUCT} = $this;

        if (\XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews == true) {
            $cnd->{\XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_STATUS}
                = \XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED;
        }

        return $cnd;
    }

    /**
     * Add reviews
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $reviews
     * @return Product
     */
    public function addReviews(\XLite\Module\XC\Reviews\Model\Review $reviews)
    {
        $this->reviews[] = $reviews;
        return $this;
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReviews()
    {
        return $this->reviews;
    }
}
