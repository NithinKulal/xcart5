<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Events subsystem
 */
class Event extends \XLite\Base\Singleton
{
    /**
     * Events list
     *
     * @var array
     */
    protected $events = array();

    /**
     * Trigger invalidElement event
     *
     * @param string $name    Element name
     * @param string $message Error message
     */
    public static function invalidElement($name, $message, $formIdentifier = null)
    {
        $data = [
            'name' => $name,
            'message' => $message
        ];

        if ($formIdentifier) {
            $data['form_identifier'] = $formIdentifier;
        }

        self::__callStatic('invalidElement', [ $data ]);
    }

    /**
     * Trigger invalidForm event
     *
     * @param string $name    Form name
     * @param string $message Error message
     */
    public static function invalidForm($name, $message)
    {
        $data = [
            'name' => $name,
            'message' => $message
        ];

        self::__callStatic('invalidForm', [ $data ]);
    }

    /**
     * Short event creation
     *
     * @param string $name      Event name
     * @param array  $arguments Event arguments
     */
    public static function __callStatic($name, array $arguments)
    {
        static::getInstance()->trigger(
            $name,
            0 < count($arguments) ? array_shift($arguments) : array()
        );
    }

    /**
     * Trigger event
     *
     * @param string $name      Event name
     * @param array  $arguments Event arguments OPTIONAL
     */
    public function trigger($name, array $arguments = array())
    {
        $this->events[] = array(
            'name'      => $name,
            'arguments' => $arguments,
        );
    }

    /**
     * Exclude event
     *
     * @param string $name Event name
     */
    public function exclude($name)
    {
        foreach ($this->events as $i => $event) {
            if ($event['name'] === $name) {
                unset($this->events[$i]);
            }
        }

        $this->events = array_values($this->events);
    }

    /**
     * Display events
     */
    public function display()
    {
        foreach ($this->events as $event) {
            header('event-' . $event['name'] . ': ' . json_encode($event['arguments']));
        }
    }

    /**
     * Clear list
     */
    public function clear()
    {
        $this->events = array();
    }
}
