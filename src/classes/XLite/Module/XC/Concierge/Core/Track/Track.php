<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core\Track;

use XLite\Module\XC\Concierge\Core\ATrack;

class Track extends ATrack
{
    /**
     * @param string $event
     * @param array  $properties
     */
    public function __construct($event, array $properties = [])
    {
        $this->event = $event;
        $this->properties = $properties;
    }
}
