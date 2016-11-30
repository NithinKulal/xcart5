<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
function alreadyHasSetting()
{
    $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
        ->findOneBy([
            'service_name' => 'SagePay form protocol'
        ]);

    $found = false;
    if ($method) {
        $settings = $method->getSettings();

        foreach ($settings as $key => $value) {
            if ($value->getName() === 'type') {
                $found = true;
                break;
            }
        }
    }

    return $found;
}

return function()
{
    if (!alreadyHasSetting()) {
        // Load new data
        $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);

        \XLite\Core\Database::getEM()->flush();
    }
};
