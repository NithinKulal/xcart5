<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function fixObjectNameInPageTitleOrder()
{
    \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
        'category' => 'CleanURL',
        'name' => 'object_name_in_page_title_order',
        'value' => \XLite\Core\Config::getInstance()->CleanURL->object_name_in_page_title_order == 'F' ? '1' : '',
    ]);
}

return function()
{
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    fixObjectNameInPageTitleOrder();

    \XLite\Core\Database::getEM()->flush();
};

