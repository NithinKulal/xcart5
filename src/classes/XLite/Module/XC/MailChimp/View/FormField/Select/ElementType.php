<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\FormField\Select;

/**
 * Select "Yes / No"
 */
class ElementType extends \XLite\View\FormField\Select\Regular
{
    const SELECT = 'select';
    const CHECKBOX = 'checkbox';

    /**
     * Return default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            self::SELECT    => static::t('Type: select box'),
            self::CHECKBOX  => static::t('Type: checkbox')
        );
    }
}