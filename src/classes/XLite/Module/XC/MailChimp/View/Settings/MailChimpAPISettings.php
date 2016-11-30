<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Settings;

use \XLite\Module\XC\MailChimp\Core;

/**
 * Tabs
 */
class MailChimpAPISettings extends \XLite\Module\XC\MailChimp\View\Settings\ASettings
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/settings.twig';
    }

    /**
     * Get current sections
     *
     * @return array
     */
    protected function getSections()
    {
        return array(Core\MailChimpSettings::SECTION_MAILCHIMP_API);
    }
}
