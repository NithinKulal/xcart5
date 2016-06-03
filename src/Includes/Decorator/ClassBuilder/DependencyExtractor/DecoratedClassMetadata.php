<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Decorator\ClassBuilder\DependencyExtractor;

class DecoratedClassMetadata implements \Serializable
{
    /** @var string[] */
    private $decorators;

    public function __construct($decorators)
    {
        $this->decorators = $decorators;
    }

    /**
     * @return mixed
     */
    public function getDecorators()
    {
        return $this->decorators;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        $decorators = array_map(function ($decorator) {
            return substr($decorator, strlen(LC_DIR_ROOT));
        }, $this->decorators);

        return json_encode($decorators);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     *                           The string representation of the object.
     *                           </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $decorators = json_decode($serialized);

        $this->decorators = array_map(function ($decorator) {
            return LC_DIR_ROOT . $decorator;
        }, $decorators);
    }
}