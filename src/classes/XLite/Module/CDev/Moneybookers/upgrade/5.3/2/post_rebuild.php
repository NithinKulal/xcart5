<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function cdevskrill_5_3_2_removeMethods() {
    $methodsToRemove = [
        'Moneybookers.PWY5',
        'Moneybookers.PWY6',
        'Moneybookers.PWY7',
        'Moneybookers.PWY14',
        'Moneybookers.PWY15',
        'Moneybookers.PWY17',
        'Moneybookers.PWY18',
        'Moneybookers.PWY19',
        'Moneybookers.PWY20',
        'Moneybookers.PWY21',
        'Moneybookers.PWY22',
        'Moneybookers.PWY25',
        'Moneybookers.PWY26',
        'Moneybookers.PWY28',
        'Moneybookers.PWY32',
        'Moneybookers.PWY33',
        'Moneybookers.PWY36',
    ];

    $em = \XLite\Core\Database::getEM();
    $repo = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method');
    foreach ($methodsToRemove as $serviceName) {
        $method = $repo->findOneBy([
            'service_name' => $serviceName
        ]);
        $em->remove($method);
    }

    \XLite\Core\Database::getEM()->flush();
}

return function()
{
    cdevskrill_5_3_2_removeMethods();

    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    \XLite\Core\Database::getEM()->flush();
};
