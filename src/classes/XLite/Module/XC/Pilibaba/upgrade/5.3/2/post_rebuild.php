<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    $pilibabaMethod = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
        ->findOneBy([
            'service_name'  => 'Pilibaba',
        ]);

    if ($pilibabaMethod) {
        $pilibabaPrefixSetting = \XLite\Core\Database::getRepo('XLite\Model\Payment\MethodSetting')
            ->findOneBy([
                'payment_method'    => $pilibabaMethod,
                'name'              => 'orderPrefix'
            ]);
        if ($pilibabaPrefixSetting) {
            \XLite\Core\Database::getEM()->remove($pilibabaPrefixSetting);
        }
    }

    \XLite\Core\Database::getEM()->flush();
};
