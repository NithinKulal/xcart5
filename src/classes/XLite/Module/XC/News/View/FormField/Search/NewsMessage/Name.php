<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\News\View\FormField\Search\NewsMessage;

/**
 * Name search widget
 */
class Name extends \XLite\View\FormField\Search\ASearch
{
    /**
     * Define fields
     *
     * @return array
     */
    protected function defineFields()
    {
        return array(
            array(
                static::FIELD_NAME  => 'name',
                static::FIELD_CLASS => 'XLite\View\FormField\Input\Text',
            ),
        );
    }
}
