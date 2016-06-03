<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Console;

/**
 * AMQP listener controller
 */
class AMQPListener extends \XLite\Controller\Console\AConsole
{
    /**
     * Driver 
     * 
     * @var \XLite\Core\EventDriver\AMQP
     */
    protected $driver;

    /**
     * Handle message 
     * 
     * @param \AMQPMessage $message Mesasge
     * @param string       $name    Event (queue) name
     *  
     * @return boolean
     */
    public function handleMessage(\AMQPMessage $message, $name)
    {
        $result = false;
        $data = @unserialize($message->body) ?: array();

        if (\XLite\Core\EventListener::getInstance()->handle($name, $data)) {
            $result = true;
            $this->getDriver()->sendAck($message); 
        }

        return $result;
    }

    /**
     * Preprocessor for no-action
     *
     * @return void
     */
    protected function doNoAction()
    {
        $driver = $this->getDriver();
        if ($driver) {
            foreach (\XLite\Core\EventListener::getInstance()->getEvents() as $name) {
                $object = $this;
                $listener = function (\AMQPMessage $message) use ($object, $name) {
                    return $object->handleMessage($message, $name);
                };
                $driver->consume($name, $listener);
            }

            do {
                $this->wait();
            } while ($this->checkCycle());
        }
    }

    /**
     * Check wait cycle 
     * 
     * @return boolean
     */
    protected function checkCycle()
    {
        return (bool)\XLite\Core\Request::getInstance()->permanent;
    }

    /**
     * Wait
     * 
     * @return void
     */
    protected function wait()
    {
        $this->getDriver()->wait();
        if (function_exists('pcntl_signal_dispatch')) {
            pcntl_signal_dispatch();
        }
    }

    /**
     * Get driver 
     * 
     * @return \XLite\Core\EventDriver\AMQP
     */
    protected function getDriver()
    {
        if (!isset($this->driver)) {
            $this->driver = \XLite\Core\EventDriver\AMQP::isValid() ? new \XLite\Core\EventDriver\AMQP : false;
        }

        return $this->driver;
    } 
}
