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

    \XLite\Core\Database::getEM()->flush();

    $tablePrefix = \XLite\Core\Database::getInstance()->getTablePrefix();

    // Update 'orderby' value of core options to increase difference between values (BUG-1412)

    $option = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(array('name' => 'anonymous_zipcode', 'category' => 'Shipping'));

    if ($option && $option->getOrderby() < 1000) {
        // Found core option with non-updated orderby - suggest that hook was not loaded yet...
        $tableName = $tablePrefix . 'config';
        $queries = array();
        $queries[] = 'UPDATE `' . $tableName . '` SET `orderby` = `orderby` * 100 WHERE `category` NOT REGEXP(\'' . preg_quote('\\\\') . '\')';
        \XLite\Core\Database::getInstance()->executeQueries($queries);
    }
};
