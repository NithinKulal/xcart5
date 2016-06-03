<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    $method = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')->findOneBy(
        array(
            'service_name' => 'PaypalAdaptive'
        )
    );

    if ($method) {
        $en = $method->getTranslation('en');
        if ($en) {
            $en->setAltAdminDescription(
                "This method provides automatic transfer of payments to vendor users (the type of users enabled by the Multi-vendor module). The method may not be activated unless the module <a href='admin.php?target=addons_list_marketplace&moduleName=XC\MultiVendor' target='_blank'>Multi-vendor</a> is installed and enabled. To use this method, store administrator is required to have an Application ID for PayPal Adaptive Payments API."
            );
        }

        $ru = $method->getTranslation('ru');
        if ($ru) {
            $ru->setName('PayPal Adaptive payments');
            $ru->setTitle('PayPal');
            $ru->setAltAdminDescription(
                'Данный метод обеспечивает автоматический перевод средств пользователям типа "вендор" (доступны в пакете Multi-vendor). Данный метод не может быть активирован, если модуль <a href="admin.php?target=addons_list_marketplace&moduleName=XC\MultiVendor" target="_blank">Multi-vendor</a> выключен или отсутствует. Для пользования данным методом администратору магазина необходимо получить Application ID для доступа к PayPal Adaptive Payments API.'
            );
        }
    }

    \XLite\Core\Database::getEM()->flush();
};
