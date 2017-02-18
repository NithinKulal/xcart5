<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core;

abstract class AMessage
{
    const TYPE_IDENTIFY = 'identify';
    const TYPE_TRACK    = 'track';
    const TYPE_PAGE     = 'page';
    const TYPE_SCREEN   = 'screen';
    const TYPE_GROUP    = 'group';
    const TYPE_ALIAS    = 'alias';
    const TYPE_RESET    = 'reset';

    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return array
     */
    abstract public function getArguments();

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type'      => $this->getType(),
            'arguments' => $this->getArguments(),
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return Mediator::getInstance()->getOptions();
    }
}
