<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    // Should use 'A' instead of constant
    // because constant already removed from a code base in the moment
    if ('A' === \XLite\Core\Config::getInstance()->XC->Reviews->whoCanLeaveFeedback) {
        \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption(
            array(
                'category' => 'XC\Reviews',
                'name'     => 'whoCanLeaveFeedback',
                'value'    => \XLite\Module\XC\Reviews\Model\Review::REGISTERED_CUSTOMERS,
            )
        );

        \XLite\Core\Database::getEM()->flush();
        \XLite\Core\Config::updateInstance();
    }
};
