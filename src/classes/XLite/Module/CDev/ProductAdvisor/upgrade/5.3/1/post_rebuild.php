<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function () {
    $repo = \XLite\Core\Database::getRepo('XLite\Model\Config');
    
    $elements = $repo->findBy([
        'name' => [
            'sep_product_advisor_na_1',
            'sep_product_advisor_na_2',
            'sep_product_advisor_cs_1',
            'sep_product_advisor_cs_2',
        ],
        'category' => 'CDev\ProductAdvisor'
    ]);

    foreach ($elements as $element) {
        $repo->delete($element, false);
    }
    \XLite\Core\Database::getEM()->flush();
};
