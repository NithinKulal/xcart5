<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    $config = \XLite\Core\Database::getRepo('\XLite\Model\Config')->findOneBy([
        'category'  => 'CDev\GoogleAnalytics',
        'name'      => 'debug_mode'
    ]);

    if (!$config && \Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    \XLite\Core\Database::getEM()->flush();
    \XLite\Core\Database::getEM()->clear();
};
