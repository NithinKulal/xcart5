<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

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
    //skins which should copy layout type from default to home
    $skins_to_copy = [
        'XC\ColorSchemes'
    ];

    $skin = \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule();

    if (!$skin || in_array($skin->getActualName(), $skins_to_copy)) {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'Layout',
            'name' => 'layout_type_' . \XLite\Core\Layout::LAYOUT_GROUP_HOME,
            'value' => \XLite\Core\Config::getInstance()->Layout->layout_type,
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

    \XLite\Core\Database::getEM()->flush();
};

