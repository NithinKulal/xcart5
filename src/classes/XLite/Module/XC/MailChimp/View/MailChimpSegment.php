<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\MailChimp\View;

/**
 * MailChimp mail lists
 *
 * @ListChild (list="admin.center", zone="admin", weight="200")
 */
class MailChimpSegment extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $return = parent::getAllowedTargets();

        $return[] = 'mailchimp_segment';

        return $return;
    }

    /**
     * Get directory where template is located
     *
     * @return string
     */
    public function getDir()
    {
        return 'modules/XC/MailChimp';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/mailchimp_segment.twig';
    }
}
