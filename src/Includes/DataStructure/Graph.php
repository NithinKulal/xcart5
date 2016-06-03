<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\DataStructure;

/**
 * Graph 
 *
 */
class Graph
{
    /**
     * Reserved key for root node
     */
    const ROOT_NODE_KEY = '____ROOT____';

    /**
     * Node unique key
     *
     * @var string
     */
    protected $key;

    /**
     * Node children
     *
     * @var array
     */
    protected $children = array();

    // {{{ Constructor and common getters

    /**
     * Constructor
     *
     * @param string $key Node unique key OPTIONAL
     *
     * @return void
     */
    public function __construct($key = self::ROOT_NODE_KEY)
    {
        $this->setKey($key);
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Check for root node
     *
     * @param string $key Key to check OPTIONAL
     *
     * @return void
     */
    public function isRoot($key = null)
    {
        return static::ROOT_NODE_KEY === ($key ?: $this->getKey());
    }

    // }}}

    // {{{ Methods to modify graph

    /**
     * Add child node
     *
     * @param \Includes\DataStructure\Graph $node Node to add
     *
     * @return void
     */
    public function addChild(\Includes\DataStructure\Graph $node)
    {
        $this->children[] = $node;
    }

    /**
     * Remove child node
     *
     * @param \Includes\DataStructure\Graph $node Node to remove
     *
     * @return void
     */
    public function removeChild(\Includes\DataStructure\Graph $node)
    {
        // Check all children
        foreach ($this->getChildren() as $index => $child) {

            // Deletion criteria - keys are equal
            if ($node->getKey() === $child->getKey()) {
                unset($this->children[$index]);
            }
        }
    }

    /**
     * Set node key
     *
     * @param string $key Key to set
     *
     * @return void
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * So called "re-plant" operation: change node parent
     *
     * @param \Includes\DataStructure\Graph $oldParent Replant from
     * @param \Includes\DataStructure\Graph $newParent Replant to
     *
     * @return void
     */
    public function replant(\Includes\DataStructure\Graph $oldParent, \Includes\DataStructure\Graph $newParent)
    {
        $oldParent->removeChild($this);
        $newParent->addChild($this);
    }

    // }}}

    // {{{ Methods to iterate over the graph

    /**
     * Common method to iterate over the tree
     *
     * @param callback                      $callback  Callback to perform on each node
     * @param boolean                       $invert    Flag OPTIONAL
     * @param \Includes\DataStructure\Graph $parent    Parent node (this param is needed for recursion) OPTIONAL
     * @param boolean                       $isStarted Flag OPTIONAL
     *
     * @return void
     */
    public function walkThrough($callback, $invert = false, \Includes\DataStructure\Graph $parent = null, $isStarted = false)
    {
        // Condition to avoid callback on the root node
        if ($isStarted && $invert) {
            call_user_func_array($callback, array($this, $parent));
        }

        // Recursive call on all child nodes
        foreach ($this->getChildren() as $node) {
            $node->{__FUNCTION__}($callback, $invert, $isStarted ? $this : null, true);
        }

        // Condition to avoid callback on the root node
        if ($isStarted && !$invert) {
            call_user_func_array($callback, array($this, $parent));
        }
    }

    /**
     * Iterate to first coincidence
     * 
     * @param callback                      $callback  Callback to perform on each node
     * @param \Includes\DataStructure\Graph $parent    Parent node (this param is needed for recursion) OPTIONAL
     * @param boolean                       $isStarted Flag OPTIONAL
     *  
     * @return boolean
     */
    public function walkFirst($callback, \Includes\DataStructure\Graph $parent = null, $isStarted = false)
    {
        $result = false;

        // Condition to avoid callback on the root node
        if ($isStarted) {
            $result = call_user_func_array($callback, array($this, $parent));
        }

        // Recursive call on all child nodes
        if (!$result) {
            foreach ($this->getChildren() as $node) {
                $result = $node->{__FUNCTION__}($callback, $isStarted ? $this : null, true);
                if ($result) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Find all nodes by key
     *
     * @param string $key Key to search OPTIONAL
     *
     * @return array
     */
    public function findAll($key = null)
    {
        $searchResult = array();

        $this->walkThrough(
            function (\Includes\DataStructure\Graph $node) use ($key, &$searchResult) {
                if (!isset($key) || $node->getKey() == $key) {
                    $searchResult[] = $node;
                }
            }
        );

        return $searchResult;
    }

    /**
     * Find node by key
     *
     * @param string $key Key to search
     *
     * @return array
     */
    public function find($key)
    {
        $searchResult = null;

        $this->walkFirst(
            function (\Includes\DataStructure\Graph $node) use ($key, &$searchResult) {
                if ($node->getKey() == $key) {
                    $searchResult = $node;
                }

                return (bool)$searchResult;
            }
        );

        return $searchResult;

        return \Includes\Utils\ArrayManager::getIndex($this->findAll($key), 0, true);
    }

    // }}}

    // {{{ Integrity check

    /**
     * Check graph integrity
     *
     * @return void
     */
    public function checkIntegrity()
    {
    }

    // }}}

    // {{{ Error handling

    /**
     * Method to fire an error
     *
     * @param string                        $code Error code (or message)
     * @param \Includes\DataStructure\Graph $node Node  Node caused the error OPTIONAL
     *
     * @return void
     */
    public function handleError($code, \Includes\DataStructure\Graph $node = null)
    {
        \Includes\ErrorHandler::fireError($this->prepareErrorMessage($code, $node));
    }

    /**
     * Prepare and return error message
     *
     * @param string                        $code Error code (or message)
     * @param \Includes\DataStructure\Graph $node Node  Node caused the error OPTIONAL
     *
     * @return string
     */
    protected function prepareErrorMessage($code, \Includes\DataStructure\Graph $node = null)
    {
        return $code . ' (' . $this->getKey() . ($node ? (', ' . $node->getKey()) : '') . ')';
    }

    // }}}

    // {{{ Visualization

    /**
     * Visualize tree
     *
     * @param \Includes\DataStructure\Graph $root   Root node of current level OPTIONAL
     * @param integer                       $offset Level offset OPTIONAL
     *
     * @return void
     */
    public function draw(\Includes\DataStructure\Graph $root = null, $offset = 0)
    {
        // Recursive call support
        if (!isset($root)) {
            $root = $this;
        }

        // Walk through all nodes
        foreach ($root->getChildren() as $child) {

            // Output
            echo (str_repeat('|__', floor($offset / 2)) . $child->getKey() . $this->drawAdditional($child) . '<br />');

            // Recursive call: next level
            $this->{__FUNCTION__}($child, $offset + 2);
        }
    }

    /**
     * For additional info
     *
     * @param \Includes\DataStructure\Graph $node Current node
     *
     * @return string
     */
    protected function drawAdditional(\Includes\DataStructure\Graph $node)
    {
        return '';
    }

    // }}}
}
