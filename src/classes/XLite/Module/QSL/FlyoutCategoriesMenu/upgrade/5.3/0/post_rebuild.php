<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    $fcm_show_icons_option = \XLite\Core\Database::getRepo('\XLite\Model\Config')->findOneBy(
        array(
            'category' => 'QSL\FlyoutCategoriesMenu',
            'name'     => 'fcm_show_icons',
        )
    );

    if ($fcm_show_icons_option) {
        \XLite\Core\Database::getEM()->remove($fcm_show_icons_option);
        \XLite\Core\Database::getEM()->flush();
    }
};
