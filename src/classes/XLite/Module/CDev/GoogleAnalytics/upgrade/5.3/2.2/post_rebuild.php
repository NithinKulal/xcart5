<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function CDevGoogleAnalytics_5322Hook_fixConfigValue()
{
    $config = \XLite\Core\Database::getRepo('\XLite\Model\Config')->findOneBy([
        'category'  => 'CDev\GoogleAnalytics',
        'name'      => 'debug_mode'
    ]);

    if ($config) {
        $config->setValue($config->getValue() === 'N' ? 0 : 1);
    }
}

return function () {
    CDevGoogleAnalytics_5322Hook_fixConfigValue();

    \XLite\Core\Database::getEM()->flush();
    \XLite\Core\Database::getEM()->clear();

    \XLite\Core\Config::updateInstance();
};
