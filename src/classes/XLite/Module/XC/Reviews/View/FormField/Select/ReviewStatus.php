<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Reviews\View\FormField\Select;

/**
 * Review status selection widget
 */
class ReviewStatus extends \XLite\View\FormField\Select\ASelect
{
    /**
     * Return default options list
     *
     * @return array
     */
    public function getDefaultOptions()
    {
        return array(
            '' => static::t('All statuses'),
            \XLite\Module\XC\Reviews\Model\Review::STATUS_PENDING  => static::t('Pending'),
            \XLite\Module\XC\Reviews\Model\Review::STATUS_APPROVED => static::t('Published'),
        );
    }
}
