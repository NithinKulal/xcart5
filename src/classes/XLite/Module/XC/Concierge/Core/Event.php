<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core;

abstract class Event extends \XLite\Core\Event implements \XLite\Base\IDecorator
{
    public function display()
    {
        $events = Mediator::getInstance()->getMessages();
        if ($events) {
            $this->trigger('concierge.push', ['list' => $events]);
        }

        parent::display();
    }
}
