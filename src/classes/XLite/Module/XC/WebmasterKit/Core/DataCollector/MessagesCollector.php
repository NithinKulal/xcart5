<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\XC\WebmasterKit\Core\DataCollector;


class MessagesCollector extends \XLite\Base\Singleton
{
    /**
     * List of messages
     *
     * @var array
     */
    protected $messages = [];
    
    /**
     * @return array
     */
    public function getCollected()
    {
        return $this->messages;
    }

    /**
     * Add memory measure point
     *
     * @return void
     */
    public function addMessage($message, $startTime = 0)
    {
        $this->messages[] = '[' . number_format(microtime(true) - $startTime, 4) . ']: ' . $message;
    }
}