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

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {    $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
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

        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }

    fillInImagesSettings();
};

function fillInImagesSettings()
{
    // Bind current settings with current skin
    $currentSkin = \XLite\Core\Database::getRepo('XLite\Model\Module')->getCurrentSkinModule();
    $currentSkinActualName = $currentSkin && $currentSkin->getActualName() !== "XC\ColorSchemes"
        ? $currentSkin->getActualName()
        : 'default';

    $currentImageSettings = \XLite\Core\Database::getRepo('XLite\Model\ImageSettings')->findAll();

    if ($currentImageSettings) {
        foreach ($currentImageSettings as $setting) {
            $setting->setModuleName(
                $currentSkinActualName
            );
        }
    }

    \XLite\Core\Database::getEM()->flush();

    // Load default settings if skin is not default
    if ($currentSkinActualName !== 'default') {
        $defaultSizesYamlPath = LC_DIR_ROOT . 'sql' . LC_DS . 'xlite_data.yaml';

        \XLite\Core\Database::getInstance()->loadFixturesFromYaml(
            $defaultSizesYamlPath,
            [
                'allowedModels' => [
                    'XLite\Model\ImageSettings'
                ]
            ]
        );
    }

    // Load all other module's settings
    $skinModules = \XLite\Core\Database::getRepo('XLite\Model\Module')->getSkinModules();
    foreach ($skinModules as $module) {
        if ($module->getActualName() === $currentSkinActualName) {
            continue;
        }

        list($author, $name) = explode('\\', $module->getActualName());
        $dir = \Includes\Utils\ModulesManager::getAbsoluteDir($author, $name);

        $installYamlPath = $dir . 'install.yaml';

        \XLite\Core\Database::getInstance()->loadFixturesFromYaml(
            $installYamlPath,
            [
                'allowedModels' => [
                    'XLite\Model\ImageSettings'
                ]
            ]
        );
    }
}
