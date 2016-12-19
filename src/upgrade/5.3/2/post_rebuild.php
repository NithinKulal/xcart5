<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function upgrade532TryChangeDefaultConfig()
{
    $configFile = LC_DIR_ROOT . 'etc/config.php';

    if (\Includes\Utils\FileManager::isFileWriteable($configFile)) {
        $origContent = file_get_contents($configFile);

        $desirableValue = 'use_language_url = "N"';

        // If value already correct - quit
        if (strpos($origContent, $desirableValue) !== false) {
            return;
        }

        $toFind = 'use_language_url = "Y"';
        $toReplace = $desirableValue;

        // If there is no such value - try to create
        if (strpos($origContent, $toFind) === false) {
            $toFind = '[clean_urls]';
            $toReplace = <<<PHP
[clean_urls]
; Is use urls like domain.com/LG for languages
; possible values "Y", "N"
; Changing this setting requires to re-deploy your store
use_language_url = "N"

PHP;
        }

        $newContent = str_replace($toFind, $toReplace, $origContent);

        if ($newContent != $origContent) {
            file_put_contents($configFile, $newContent);
        }
    }
}

function removeRecentOrdersConfig() {
    $list = \XLite\Core\Database::getRepo('XLite\Model\Config')
        ->findBy([
            'name'          => 'recent_orders',
            'category'      => 'General',
        ]);

    foreach ($list as $value) {
        \XLite\Core\Database::getEM()->remove($value);
    }
}

function fixForceProductOptionsConfig($oldValue) {
    $newValue = !in_array($oldValue, array('0', '', 'N'), true) ? 'category' : '';

    \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
        array('name' => 'force_choose_product_options', 'category' => 'General', 'value' => $newValue)
    );
}

function setHomeGroupLayoutTypeValue() {
    \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
        'category' => 'Layout',
        'name' => 'layout_type_' . \XLite\Core\Layout::LAYOUT_GROUP_HOME,
        'value' => \XLite\Core\Config::getInstance()->Layout->layout_type,
    ]);

    $skin = \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule();

    if ($skin && $skin->getActualName() === 'XC\CrispWhiteSkin') {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'Layout',
            'name' => 'layout_type_' . \XLite\Core\Layout::LAYOUT_GROUP_HOME,
            'value' => \XLite\Core\Layout::LAYOUT_ONE_COLUMN,
        ]);
    }
}

return function()
{
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    $oldOption = \XLite\Core\Database::getRepo('XLite\Model\Config')->findOneBy(
        array('name' => 'force_choose_product_options', 'category' => 'General')
    );
    $oldValue = $oldOption ? $oldOption->getValue() : '';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    removeRecentOrdersConfig();
    fixForceProductOptionsConfig($oldValue);
    setHomeGroupLayoutTypeValue();
    upgrade532TryChangeDefaultConfig();


    \XLite\Core\Database::getEM()->flush();
};

