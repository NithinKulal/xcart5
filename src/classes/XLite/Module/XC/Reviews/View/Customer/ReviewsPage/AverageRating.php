<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Customer\ReviewsPage;

/**
 * Reviews list widget
 *
 */
class AverageRating extends \XLite\Module\XC\Reviews\View\AverageRating
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Reviews/reviews_page/rating/body.twig';
    }
}
