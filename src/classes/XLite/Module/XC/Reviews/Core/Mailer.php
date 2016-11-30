<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\Core;

/**
 * Mailer
 */
abstract class Mailer extends \XLite\Core\Mailer implements \XLite\Base\IDecorator
{
    const NEW_REVIEW_NOTIFICATION = 'modules/XC/Reviews/new_review';

    /**
     * Send new review message
     *
     * @param \XLite\Module\XC\Reviews\Model\Review $review Review
     *
     * @return string
     */
    public static function sendNewReview(\XLite\Module\XC\Reviews\Model\Review $review)
    {
        static::register('review', $review);

        static::compose(
            'siteAdmin',
            static::getOrdersDepartmentMail(),
            static::getSiteAdministratorMail(),
            static::NEW_REVIEW_NOTIFICATION,
            array(),
            true,
            \XLite::ADMIN_INTERFACE,
            static::getMailer()->getLanguageCode(\XLite::ADMIN_INTERFACE)
        );

        return static::getMailer()->getLastError();
    }
}
