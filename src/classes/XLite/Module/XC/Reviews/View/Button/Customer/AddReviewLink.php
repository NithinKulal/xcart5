<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\Button\Customer;

/**
 * Add review button widget
 *
 */
class AddReviewLink extends \XLite\Module\XC\Reviews\View\Button\Customer\AddReview
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Reviews/button/popup_link.twig';
    }

    /**
     * Return CSS class
     *
     * @return string
     */
    protected function getClass()
    {
        return ' add-review ';
    }
}
