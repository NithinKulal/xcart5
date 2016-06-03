<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\DataSet;

/**
 * Collection
 */
class Collection extends \Doctrine\Common\Collections\ArrayCollection
{
    // {{{ Elements checking

    /**
     * Constructor
     *
     * @param array $elements Elements OPTIONAL
     *
     * @return void
     */
    public function __construct(array $elements = array())
    {
        parent::__construct($elements);
        $this->filterElements();
    }

    /**
     * ArrayAccess implementation of  offsetSet()
     *
     * @param mixed $offset Offset
     * @param mixed $value  Value
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if ($this->checkElement($value, $offset)) {
            parent::offsetSet($offset, $value);
        }
    }

    /**
     * Filter elements
     *
     * @return void
     */
    protected function filterElements()
    {
        foreach ($this as $i => $e) {
            if (!$this->checkElement($e, $i)) {
                unset($this[$i]);
            }
        }
    }

    /**
     * Check element
     *
     * @param mixed $element Element
     * @param mixed $key     Element key
     *
     * @return boolean
     */
    protected function checkElement($element, $key)
    {
        return true;
    }

    // }}}

    // {{{ Siblings

    /**
     * Get element previous siblings
     *
     * @param mixed $element Element
     *
     * @return array
     */
    public function getPreviousSiblings($element)
    {
        $previous = array();

        foreach ($this as $i => $e) {
            if ($e == $element) {
                break;
            }

            $previous[$i] = $e;
        }

        return $previous;
    }

    /**
     * Get element next siblings
     *
     * @param mixed $element Element
     *
     * @return array
     */
    public function getNextSiblings($element)
    {
        $next = array();
        $found = false;

        foreach ($this as $i => $e) {
            if ($found) {
                $next[$i] = $e;
            }

            if ($e == $element) {
                $found = true;
            }
        }

        return $next;
    }

    // }}}
}
