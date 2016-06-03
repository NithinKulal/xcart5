<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\TinyMCE\View\Model;

/**
 * Notification view model
 */
class NotificationCommon extends \XLite\View\Model\NotificationCommon implements \XLite\Base\IDecorator
{
    /**
     * Prepare emailNotificationCustomerHeader field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsEmailNotificationCustomerHeader($data)
    {
        $data[\XLite\View\FormField\Textarea\Advanced::PARAM_CONVERT_URLS] = false;

        return $data;
    }

    /**
     * Prepare emailNotificationCustomerSignature field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsEmailNotificationCustomerSignature($data)
    {
        $data[\XLite\View\FormField\Textarea\Advanced::PARAM_CONVERT_URLS] = false;

        return $data;
    }

    /**
     * Prepare emailNotificationAdminHeader field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsEmailNotificationAdminHeader($data)
    {
        $data[\XLite\View\FormField\Textarea\Advanced::PARAM_CONVERT_URLS] = false;

        return $data;
    }

    /**
     * Prepare emailNotificationAdminSignature field parameters
     *
     * @param array $data Parameters
     *
     * @return array
     */
    protected function prepareFieldParamsEmailNotificationAdminSignature($data)
    {
        $data[\XLite\View\FormField\Textarea\Advanced::PARAM_CONVERT_URLS] = false;

        return $data;
    }
}
