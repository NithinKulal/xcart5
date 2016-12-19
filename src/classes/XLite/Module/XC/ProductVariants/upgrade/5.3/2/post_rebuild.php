<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    // BUG-4336
    if (!\XLite\Core\Database::getRepo('XLite\Model\Module')->isModuleEnabled('XC\ProductVariants')) {
        return;
    }

    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    \XLite\Core\Database::getEM()->flush();

    $qb = \XLite\Core\Database::getEM()->createQueryBuilder();
    $qb->update('XLite\Model\QuickData', 'qd')
        ->set('qd.minPrice', 'qd.price')
        ->set('qd.maxPrice', 'qd.price')
        ->getQuery()
        ->execute();
};
