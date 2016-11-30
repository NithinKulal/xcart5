<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\Core;

/**
 * MailChimp core class
 */
class MailChimpSettings extends \XLite\Base\Singleton
{
    const SECTION_MAILCHIMP_API     = 'settings';

    /**
     * Get all option sections
     *
     * @return array
     */
    public function getAllSections()
    {
        return array(
            self::SECTION_MAILCHIMP_API
        );
    }
}
