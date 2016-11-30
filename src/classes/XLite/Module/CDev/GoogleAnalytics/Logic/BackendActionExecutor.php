<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\GoogleAnalytics\Logic;


use XLite\Module\CDev\GoogleAnalytics;
use XLite\Module\CDev\GoogleAnalytics\Logic\Action\IBackendAction;

/**
 * Class BackendActionExecutor
 */
class BackendActionExecutor
{
    const GA_API_ENDPOINT = 'https://www.google-analytics.com/collect';

    /**
     * @param IBackendAction $action
     *
     * @return boolean
     */
    public static function execute(IBackendAction $action)
    {
        if (!$action->isBackendApplicable()) {
            return false;
        }

        try {
            \XLite\Core\Translation::setTmpTranslationCode(\XLite\Core\Config::getInstance()->General->default_language);

            $data = $action->getActionDataForBackend();

            \XLite\Core\Translation::setTmpTranslationCode(null);

            $request = new \XLite\Core\HTTP\Request(static::GA_API_ENDPOINT);
            $request->verb = 'POST';
            $request->body = http_build_query($data);
            $response = $request->sendRequest();

            if (GoogleAnalytics\Main::isDebugMode()) {
                \XLite\Logger::logCustom('google_analytics_debug', [
                    'request data'  => $data,
                    'response data' => $response,
                ]);
            }

        } catch(\Exception $e) {
            \XLite\Logger::logCustom('google_analytics_failed', isset($data) ? $data : $action);
        }

        return (bool) $response;
    }

}