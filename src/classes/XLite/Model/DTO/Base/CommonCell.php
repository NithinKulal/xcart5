<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\DTO\Base;

use XLite\Core\CommonCell as CoreCommonCell;

class CommonCell extends CoreCommonCell implements \ArrayAccess, \Countable
{
    /**
     * @param mixed $offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->properties);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->properties[$offset];
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (null !== $offset) {
            $this->properties[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->properties[$offset]);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_map(function ($item) {
            return is_object($item) && method_exists($item, 'toArray')
                ? $item->toArray()
                : (is_array($item) ? $item : (string) $item);

        }, $this->properties);
    }

    /**
     * Count elements of an object
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.

     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->properties);
    }
}
