<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\EventDriver;

/**
 * AMQP-based event driver 
 */
class AMQP extends \XLite\Core\EventDriver\AEventDriver
{
    const REDECLARE_TTL = 3600;

    /**
     * Connection
     *
     * @var \AMQPConnection
     */
    protected $connection;

    /**
     * Channel
     *
     * @var \AMQPChannel
     */
    protected $channel;

    /**
     * Check driver
     *
     * @return boolean
     */
    public static function isValid()
    {
        return (bool)static::getInstance()->getChannel();
    }

    /**
     * Get driver code
     *
     * @return string
     */
    public static function getCode()
    {
        return 'amqp';
    }

    /**
     * Current driver is blocking
     *
     * @return boolean
     */
    public function isBlocking()
    {
        return true;
    }

    /**
     * Fire event
     *
     * @param string $name      Event name
     * @param array  $arguments Arguments OPTIONAL
     *
     * @return boolean
     */
    public function fire($name, array $arguments = array())
    {
        $result = true;

        $channel = $this->getChannel();
        try {
            $this->redeclareQueue($name);
            $channel->basic_publish(
                new \AMQPMessage(serialize($arguments), array('content_type' => 'text/plain')),
                $this->getExchange(),
                $name
            );

        } catch (\Exception $e) {
            $result = false;
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Get channel
     *
     * @param boolean $reset Reset flag OPTIONAL
     *
     * @return \AMQPChannel
     */
    public function getChannel($reset = false)
    {
        if (!$this->channel || $reset) {
            require_once LC_DIR_LIB . 'AMQP' . LC_DS . 'amqp.inc';

            try {
                $this->initializeConnection();
            } catch (\Exception $e) {
            }
        }

        return $this->channel;
    }

    /**
     * Redeclare queue 
     * 
     * @param string $name Queue name
     *  
     * @return void
     */
    public function redeclareQueue($name)
    {
        $key = 'amqp.queue.' . $name;

        $entity = \XLite\Core\Database::getRepo('XLite\Model\TmpVar')->findOneBy(array('name' => $key));

        $time = \XLite\Core\Converter::time();

        if (!$entity) {
            $entity = new \XLite\Model\TmpVar;
            $entity->setName($key);
            $entity->setValue($time);
            \XLite\Core\Database::getEM()->persist($entity);

            $this->declareQueue($name);

        } elseif ($entity->getValue() + static::REDECLARE_TTL < $time) {

            $entity->setValue($time);
            $this->declareQueue($name);
        }
    }

    /**
     * Declare exchange and queue
     *
     * @return boolean
     */
    public function declareQueue($name)
    {
        $result = true;
        $channel = $this->getChannel();
        $exchange = $this->getExchange();

        try {
            $channel->exchange_declare($exchange, 'direct', false, true, false);
            $channel->queue_declare($name, false, true, false, false);
            $channel->queue_bind($name, $exchange, $name);

        } catch (\Exception $e) {
            $result = false;
            \XLite\Logger::getInstance()->registerException($e);
        }

        return $result;
    }

    /**
     * Consume queue
     * 
     * @param string $queue    Queue name
     * @param mixed  $listener Callable listener
     * @param string $tag      Consumer tag OPTIONAL
     *  
     * @return void
     */
    public function consume($queue, $listener, $tag = null)
    {
        $channel = $this->getChannel();

        if ($channel) {
            $this->redeclareQueue($queue);
            $channel->basic_consume(
                $queue,
                $tag,
                false,
                false,
                false,
                false,
                $listener
            );
        }

    }

    /**
     * Send acknowledge
     * 
     * @param \AMQPMessage $message Mesasge
     *  
     * @return void
     */
    public function sendAck(\AMQPMessage $message)
    {
        $message->delivery_info['channel']->basic_ack($message->delivery_info['delivery_tag']);
    }

    /**
     * Wait messages
     * 
     * @return void
     */
    public function wait()
    {
        $channel = $this->getChannel();

        if ($channel && 0 < count($channel->callbacks)) {
            $channel->wait();
        }
    }

    /**
     * Get exchange name
     *
     * @return string
     */
    protected function getExchange()
    {
        return \XLite::getInstance()->getOptions(array('amqp', 'exchange')) ?: 'xlite';
    }

    /**
     * Initialize connection
     *
     * @return void
     */
    protected function initializeConnection()
    {
        $this->channel = null;
        $this->connection = null;

        if (function_exists('bcmod')) {
            $config = \XLite::getInstance()->getOptions(array('amqp'));

            $this->connection = new \AMQPConnection(
                $config['host'],
                $config['port'],
                $config['user'],
                $config['password'],
                $config['vhost']
            );
            $this->channel = $this->connection->channel();
        }
    }

}

