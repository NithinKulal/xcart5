<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\FormField\Select;

/**
 * Review type selection widget
 */
class ReviewType extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return array(
            '' => static::t('Reviews and ratings'),
            \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_TYPE_RATINGS_ONLY => static::t('Ratings only'),
            \XLite\Module\XC\Reviews\Model\Repo\Review::SEARCH_TYPE_REVIEWS_ONLY => static::t('Reviews only'),
        );
    }
}
