<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core\Message;

use XLite\Module\XC\Concierge\Core\AMessage;

class Reset extends AMessage
{
    public function getType()
    {
        return static::TYPE_RESET;
    }

    public function getArguments()
    {
        return [];
    }
}
