<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function isSettingExists($adaptiveMethod)
{
    return \XLite\Core\Database::getRepo('XLite\Model\Payment\MethodSetting')
        ->findOneBy(array(
            'payment_method'    => $adaptiveMethod,
            'name'              => 'matchCriteria'
        )
    );
}
function paypal_532_postrebuild_addMatchCriteriaSetting() {
    $adaptiveMethod = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
         ->findOneBy(array('service_name' => 'PaypalAdaptive'));

    if ($adaptiveMethod && !isSettingExists($adaptiveMethod)) {
        $setting = new \XLite\Model\Payment\MethodSetting();

        $setting->setName('matchCriteria');
        $setting->setValue('none');
        $setting->setPaymentMethod($adaptiveMethod);

        $adaptiveMethod->addSettings($setting);
    }
}
function paypal_532_postrebuild_renamePartnerHosted() {
    $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
        ->findOneBy(array('service_name' => 'PayflowTransparentRedirect'));

    if ($method) {
        $method->setEditLanguage('en');
        $method->setName('PayPal Partner Hosted with PCI Compliance');
    }
}

return function()
{
    // Loading data to the database from yaml file
    $yamlFile = __DIR__ . LC_DS . 'post_rebuild.yaml';

    if (\Includes\Utils\FileManager::isFileReadable($yamlFile)) {
        \XLite\Core\Database::getInstance()->loadFixturesFromYaml($yamlFile);
    }
    
    paypal_532_postrebuild_addMatchCriteriaSetting();
    paypal_532_postrebuild_renamePartnerHosted();

    \XLite\Core\Database::getEM()->flush();
};
