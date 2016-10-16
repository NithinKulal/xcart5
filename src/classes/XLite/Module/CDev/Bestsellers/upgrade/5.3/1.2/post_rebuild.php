<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    $changed = false;

    if (in_array(\XLite\Core\Config::getInstance()->General->default_products_sort_order, ['boughtAsc', 'boughtDesc'])) {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'General',
                'name'     => 'default_products_sort_order',
                'value'    => 'bought',
            )
        );
        $changed = true;
    }
    
    if (in_array(\XLite\Core\Config::getInstance()->General->default_search_sort_order, ['boughtAsc', 'boughtDesc'])) {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'General',
                'name'     => 'default_search_sort_order',
                'value'    => 'bought',
            )
        );
        $changed = true;
    }

    if ($changed) {
        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Config::updateInstance();
    }
};
