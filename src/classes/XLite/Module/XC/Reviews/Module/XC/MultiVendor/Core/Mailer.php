<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Module\XC\MultiVendor\Core;

/**
 * Mailer
 * 
 * @Decorator\Depend ({"XC\Reviews","XC\MultiVendor"})
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    /**
     * Send new review message
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $review Review
     *
     * @return string
     */
    public static function sendNewReview(\XLite\Module\XC\Reviews\Model\Review $review)
    {
        if ($review->getProduct()->getVendor()) {
            static::sendNewReviewVendor(
                $review->getProduct()->getVendor(),
                $review
            );
        } else {
            static::sendNewReviewAdmin($review);
        }
    }

    /**
     * Send new review message
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $review Review
     *
     * @return string
     */
    public static function sendNewReviewVendor(\XLite\Model\Profile $vendor, \XLite\Module\XC\Reviews\Model\Review $review)
    {
        static::register('review', $review);

        static::compose(
            'siteAdmin',
            static::getOrdersDepartmentMail(),
            $vendor->getLogin(),
            static::NEW_REVIEW_NOTIFICATION,
            array(),
            true,
            \XLite::ADMIN_INTERFACE,
            static::getMailer()->getLanguageCode(\XLite::ADMIN_INTERFACE)
        );

        return static::getMailer()->getLastError();
    }

    /**
     * Send new review message
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $review Review
     *
     * @return string
     */
    public static function sendNewReviewAdmin(\XLite\Module\XC\Reviews\Model\Review $review)
    {
        return parent::sendNewReview($review);
    }
}
