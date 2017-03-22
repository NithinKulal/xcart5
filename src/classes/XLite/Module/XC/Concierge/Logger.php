<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\Concierge;

use XLite\Module\XC\Concierge\Core\Mediator;

/**
 * Logger
 */
abstract class Logger extends \XLite\Logger implements \XLite\Base\IDecorator
{
    /**
     * @inheritdoc
     */
    public function log($message, $level = LOG_DEBUG, $trace = [])
    {
        parent::log($message, $level, $trace);

        if (in_array($level, [LOG_ERR, LOG_WARNING, LOG_CRIT, LOG_NOTICE], true)) {
            Mediator::getInstance()->throwTrack(
                'Error',
                [
                    'error'     => $message,
                    'backTrace' => $trace ?: static::getBackTrace(),
                ]
            );
        }
    }
}
