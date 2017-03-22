<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
function upgrade5328_trySetStateIdRequired()
{
    $stateField = \XLite\Core\Database::getRepo('XLite\Model\AddressField')
        ->findOneByServiceName('state_id');

    if ($stateField) {
        $stateField->setRequired(true);
    }
}

return function()
{
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    upgrade5328_trySetStateIdRequired();

    if (\Xlite\Core\Config::getInstance()->Performance->use_dynamic_image_resizing === 'N') {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
            'category' => 'Performance',
            'name'     => 'use_dynamic_image_resizing',
            'value'    => '',
        ]);
    }

    \XLite\Core\Database::getEM()->flush();
};

