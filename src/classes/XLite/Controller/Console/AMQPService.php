<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Controller\Console;

/**
 * AMQP service 
 */
class AMQPService extends \XLite\Controller\Console\AConsole
{
    /**
     * Declare queues 
     * 
     * @return void
     */
    protected function doActionDeclareQueues()
    {
        if (\XLite\Core\EventDriver\AMQP::isValid()) {
            $driver = new \XLite\Core\EventDriver\AMQP;
            foreach (\XLite\Core\EventListener::getInstance()->getEvents() as $name) {
                $this->printContent($name . ' ... ');
                if ($driver->declareQueue($name)) {
                    $this->printContent('done');

                } else {
                    $this->printContent('failed');
                }

                $this->printContent(PHP_EOL);
            }
        }
    }

    /**
     * Remove all queues 
     * 
     * @return void
     */
    protected function doActionRemoveQueues()
    {
        if (\XLite\Core\EventDriver\AMQP::isValid()) {
            $driver = new \XLite\Core\EventDriver\AMQP;
            foreach (\XLite\Core\EventListener::getInstance()->getEvents() as $name) {
                $this->printContent($name . ' ... ');
                $result = false;
                try {
                    $driver->getChannel()->queue_delete($name);
                    $result = true;

                } catch (\Exception $e) {
                    $driver->getChannel(true);
                }

                if ($result) {
                    $this->printContent('done' . PHP_EOL);

                } else {
                    $this->printContent('failed' . PHP_EOL);
                }
            }
        }
    }
}

