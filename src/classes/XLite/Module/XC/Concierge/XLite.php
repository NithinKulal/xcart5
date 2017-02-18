<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge;

/**
 * XLite
 */
abstract class XLite extends \XLite implements \XLite\Base\IDecorator
{
    /**
     * @param boolean $adminZone
     *
     * @return \XLite
     * @throws \Exception
     */
    public function run($adminZone = false)
    {
        try {
            return parent::run($adminZone);

        } catch (\Exception $exception) {
            if (static::isAdminZone()) {
                \XLite\Module\XC\Concierge\Core\Mediator::getInstance()->handleException($exception);
            }

            throw $exception;
        }
    }

    /**
     * @return \XLite
     * @throws \Exception
     */
    public function processRequest()
    {
        try {
            return parent::processRequest();

        } catch (\Exception $exception) {
            if (static::isAdminZone()) {
                \XLite\Module\XC\Concierge\Core\Mediator::getInstance()->handleException($exception);
            }

            throw $exception;
        }
    }
}
