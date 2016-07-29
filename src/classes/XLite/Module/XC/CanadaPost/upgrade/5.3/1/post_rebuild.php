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

    $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
    $qb = $repo->createPureQueryBuilder()
        ->update()
        ->set('c.type', ':newType')
        ->where('c.type = :oldType')
        ->orWhere('c.type = :oldType2')
        ->setParameter('oldType', 'XLite\View\FormField\Input\Text\Float')
        ->setParameter('oldType2', '\XLite\View\FormField\Input\Text\Float')
        ->setParameter('newType', 'XLite\View\FormField\Input\Text\FloatInput');

    $qb->getQuery()->execute();

    \XLite\Core\Database::getEM()->flush();
    \XLite\Core\Config::updateInstance();
};
