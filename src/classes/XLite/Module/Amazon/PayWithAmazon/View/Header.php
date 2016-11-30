<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View;

/**
 * Page header
 */
class Header extends \XLite\View\Header implements \XLite\Base\IDecorator
{
    /**
     * @return \XLite\Core\CommonCell
     */
    protected function getAmazonConfig()
    {
        $api = \XLite\Module\Amazon\PayWithAmazon\Main::getApi();

        return $api->getConfig();
    }

    /**
     * @return boolean
     */
    protected function isAmazonConfigured()
    {
        $api = \XLite\Module\Amazon\PayWithAmazon\Main::getApi();

        return $api->isConfigured();
    }

    /**
     * @return string
     */
    protected function isSandboxMode()
    {
        return $this->getAmazonConfig()->amazon_pa_mode === 'test' ? 'true' : 'false';
    }

    /**
     * @deprecated @todo: rewrite with responsive
     * @return string
     */
    protected function isMobileDevice()
    {
        return method_exists('\XLite\Core\Request', 'isMobileDevice') && \XLite\Core\Request::isMobileDevice()
            ? 'true'
            : 'false';
    }
}
