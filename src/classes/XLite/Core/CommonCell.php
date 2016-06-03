<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Common cell class
 */
class CommonCell extends \Includes\DataStructure\Cell implements \Iterator
{
    /**
     * Return the current element
     *
     * @return mixed
     */
    public function current()
    {
        return current($this->properties);
    }

    /**
     * Return the key of the current element
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->properties);
    }

    /**
     * Move forward to next element
     *
     * @return mixed (ignored)
     */
    public function next()
    {
        return next($this->properties);
    }

    /**
     * Rewind the Iterator to the first element
     *
     * @return mixed (ignored)
     */
    public function rewind()
    {
        return reset($this->properties);
    }

    /**
     * Checks if current position is valid
     *
     * @return boolean
     */
    public function valid()
    {
        return !is_null($this->key());
    }
}
