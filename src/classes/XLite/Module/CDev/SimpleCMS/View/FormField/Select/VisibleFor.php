<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\SimpleCMS\View\FormField\Select;

/**
 * "Visible for" selector
 *
 */
class VisibleFor extends \XLite\View\FormField\Select\Regular
{
    /**
     * Texts for labels
     */
    const ANY_VISITORS      = 'Any visitors';
    const ANONYMOUS_ONLY    = 'Anonymous users only';
    const LOGGED_IN_ONLY    = 'Logged in users only';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return array(
            'AL' => static::t(static::ANY_VISITORS),
            'A'  => static::t(static::ANONYMOUS_ONLY),
            'L'  => static::t(static::LOGGED_IN_ONLY),
        );
    }
}
