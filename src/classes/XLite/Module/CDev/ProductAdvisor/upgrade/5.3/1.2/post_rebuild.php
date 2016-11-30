<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');

    $element = $repo->findOneBy([
        'name' => 'cs_show_in_sidebar',
        'category' => 'CDev\ProductAdvisor'
    ]);

    if ($element) {
        $repo->delete($element, false);
        \XLite\Core\Database::getEM()->flush();
    }
};
