<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

function cdevskrill_5_3_2_1_removeMethods() {
    $methodsToRemove = [
        'Moneybookers.JCB',
        'Moneybookers.DIN',
        'Moneybookers.SLO',
        'Moneybookers.LSR',
    ];

    $em = \XLite\Core\Database::getEM();
    $repo = \XLite\Core\Database::getRepo('XLite\Model\Payment\Method');
    foreach ($methodsToRemove as $serviceName) {
        $method = $repo->findOneBy([
            'service_name' => $serviceName
        ]);
        if ($method) {
            $em->remove($method);
        }
    }

    \XLite\Core\Database::getEM()->flush();
}

return function()
{
    cdevskrill_5_3_2_1_removeMethods();
};
