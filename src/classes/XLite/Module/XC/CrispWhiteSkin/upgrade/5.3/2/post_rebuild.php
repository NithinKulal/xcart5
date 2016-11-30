<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

return function()
{
    \XLite\Core\Database::getRepo('XLite\Model\Config')->createOption([
        'category' => 'Layout',
        'name' => 'layout_type_' . \XLite\Core\Layout::LAYOUT_GROUP_HOME,
        'value' => \XLite\Core\Layout::LAYOUT_ONE_COLUMN,
    ]);
};
