<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
            ->findOneBy(array( 'service_name' => 'AuthorizeNet SIM' ));

    if ($method && $method->getSetting('md5_key') === null) {
        // Loading data to the database from yaml file
        $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

        if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
            \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
        }

        \XLite\Core\Database::getEM()->flush();
    }
};