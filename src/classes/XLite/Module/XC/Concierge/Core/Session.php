<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge\Core;

abstract class Session extends \XLite\Core\Session implements \XLite\Base\IDecorator
{
    //protected function createSession()
    //{
    //    parent::createSession();
    //
    //    if (\XLite\Core\Config::getInstance()->XC->Concierge->write_key && !$this->useDumpSession()) {
    //        $this->sessionImmediateCreated = true;
    //    }
    //}
}
