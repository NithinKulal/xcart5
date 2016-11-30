<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View\Settings;

use XLite\Module\XC\MailChimp\Core;

/**
 * Settings
 */
abstract class ASettings extends \XLite\View\AView
{
    /**
     * Get current sections
     *
     * @return array
     */
    abstract protected function getSections();

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $return = parent::getAllowedTargets();

        $return[] = 'mailchimp_options';

        return $return;
    }

    /**
     * Return templates directory name
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/XC/MailChimp/settings';
    }

    /**
     * Get list of available sections
     *
     * @return array
     */
    protected function getAllSections()
    {
        return Core\MailChimpSettings::getInstance()->getAllSections();
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && in_array(
                \XLite\Core\Request::getInstance()->section,
                $this->getSections()
            );
    }
}
