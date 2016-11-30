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
    $use_share_button = \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_like_send_button;

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }
    
    if ($use_share_button) {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
        $repo->createOption(
            array(
                'category' => 'CDev\GoSocial',
                'name'     => 'fb_share_use',
                'value'    => true,
            )
        );
    }

    \XLite\Core\Database::getEM()->flush();
};
