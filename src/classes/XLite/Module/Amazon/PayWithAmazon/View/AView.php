<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\Amazon\PayWithAmazon\View;

use XLite\Module\Amazon\PayWithAmazon\Main;

abstract class AView extends \XLite\View\AView implements \XLite\Base\IDecorator
{
    /**
     * @param boolean|null $adminZone
     *
     * @return array
     */
    protected function getThemeFiles($adminZone = null)
    {
        $list      = parent::getThemeFiles($adminZone);
        $method    = Main::getMethod();
        $processor = Main::getProcessor();

        if ($processor->isConfigured($method) && $method->isEnabled()) {
            $list[static::RESOURCE_JS][] = [
                'url' => $processor->getJsSdkUrl($method), // todo: allow async attribute for script tag
            ];
            $list[static::RESOURCE_JS][] = 'modules/Amazon/PayWithAmazon/func.js';

            $list[static::RESOURCE_CSS][] = 'modules/Amazon/PayWithAmazon/checkout_button/style.css';
        }

        return $list;
    }

    /**
     * @return boolean
     */
    public function isPayWithAmazonActive()
    {
        $method    = Main::getMethod();
        $processor = Main::getProcessor();

        // disable if no seller id is specified
        if (!$processor->isConfigured($method) || !$method->isEnabled()) {
            return false;
        }

        if ($this->getCart() && !$this->getCart()->checkCart()) {
            return false;
        }

        return true;
    }
}
