<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XPaymentsConnector\View\Settings;

/**
 * Warning for the disabled HTTPS
 */
class HTTPSWarning extends \XLite\Module\CDev\XPaymentsConnector\View\Settings\ASettings
{
    /**
     * Check if HTTPS options are enabled
     *
     * @return boolean
     */
    protected function isEnabledHTTPS()
    {
        return \XLite\Core\Config::getInstance()->Security->admin_security
            && \XLite\Core\Config::getInstance()->Security->customer_security;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/https_warning.twig';
    }

    /**
     * List of tabs/pages where this setting should be displayed
     *
     * @return boolean
     */
    public function getPages()
    {
        return array_keys(\XLite\Module\CDev\XPaymentsConnector\Core\Settings::getAllPages());
    }
}
