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
    
    $new = \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->na_mark_with_label;
    $upcoming = \XLite\Core\Config::getInstance()->CDev->ProductAdvisor->cs_mark_with_label;

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    if ($new) {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
        $repo->createOption(
            array(
                'category' => 'CDev\ProductAdvisor',
                'name'     => 'na_mark_with_label',
                'value'    => \XLite\Module\CDev\ProductAdvisor\View\FormField\Select\MarkProducts::PARAM_MARK_IN_CATALOG_ONLY,
            )
        );
    }

    if ($upcoming) {
        $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
        $repo->createOption(
            array(
                'category' => 'CDev\ProductAdvisor',
                'name'     => 'cs_mark_with_label',
                'value'    => \XLite\Module\CDev\ProductAdvisor\View\FormField\Select\MarkProducts::PARAM_MARK_IN_CATALOG_ONLY,
            )
        );
    }

    \XLite\Core\Database::getEM()->flush();
};
