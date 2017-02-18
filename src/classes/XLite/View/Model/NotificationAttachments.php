<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Model;

/**
 * Notification view model
 */
class NotificationAttachments extends \XLite\View\Model\Settings
{

    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'notification_attachments';

        return $result;
    }

    /**
     * Return name of web form widget class
     *
     * @return string
     */
    protected function getFormClass()
    {
        return 'XLite\View\Form\Model\NotificationAttachments';
    }

    /**
     * Check is symfony-polyfill is used instead of native mbstring
     */
    public function isMbstringWarningVisible()
    {
        return !function_exists('mb_convert_kana');
    }
}
