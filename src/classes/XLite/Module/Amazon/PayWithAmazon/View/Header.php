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
     * @return array
     */
    protected function getAmazonConfig()
    {
        $result = [];
        $method = \XLite\Module\Amazon\PayWithAmazon\Main::getMethod();
        foreach ($method->getSettings() as $setting) {
            $result[$setting->getName()] = $setting->getValue();
        }

        $result['region'] = \XLite\Module\Amazon\PayWithAmazon\View\FormField\Select\Region::getRegionByCurrency($result['region']);

        return $result;
    }

    /**
     * @return boolean
     */
    protected function isAmazonConfigured()
    {
        $method    = \XLite\Module\Amazon\PayWithAmazon\Main::getMethod();
        $processor = \XLite\Module\Amazon\PayWithAmazon\Main::getProcessor();

        return $processor->isConfigured($method);
    }

    /**
     * Returns string 'true' if test mode enabled, 'false' otherwise
     *
     * @return string
     */
    protected function isSandboxMode()
    {
        $method    = \XLite\Module\Amazon\PayWithAmazon\Main::getMethod();
        $processor = \XLite\Module\Amazon\PayWithAmazon\Main::getProcessor();

        return $processor->isTestMode($method) ? 'true' : 'false';
    }
}
