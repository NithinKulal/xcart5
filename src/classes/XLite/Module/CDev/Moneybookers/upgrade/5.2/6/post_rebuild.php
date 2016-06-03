<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    // Remove payment methods with specific service names (see BUG-2890)
    $toDelete = array(
        'Moneybookers.ENT',
        'Moneybookers.SO',
    );

    foreach ($toDelete as $sn) {
        $pm = \XLite\Core\Database::getRepo('\XLite\Model\Payment\Method')->findOneBy(
            array('service_name' => $sn)
        );
        if ($pm) {
            $pm->delete();
        }
    }
};
