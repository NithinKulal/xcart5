<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Common Graph class
 */
class CommonGraph extends \Includes\DataStructure\Graph
{
    /**
     * Parent
     *
     * @var \Includes\DataStructure\Graph
     */
    protected $parent = null;

    /**
     * Data
     *
     * @var mixed
     */
    protected $data = null;

    /**
     * Set parent
     *
     * @param \Includes\DataStructure\Graph $node Node
     *
     * @return void
     */
    public function setParent(\Includes\DataStructure\Graph $node)
    {
        $this->parent = $node;
    }

    /**
     * Returns parent
     *
     * @return \Includes\DataStructure\Graph
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set node data
     *
     * @param mixed $data Data
     *
     * @return void
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Returns node data
     *
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Add child node
     *
     * @param \Includes\DataStructure\Graph $node Node to add
     *
     * @return void
     */
    public function addChild(\Includes\DataStructure\Graph $node)
    {
        $node->setParent($this);

        parent::addChild($node);
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
        foreach ($this->getChildren() as $index => $child) {
            if ($node->getKey() === $child->getKey()) {
                $node->setParent(null);
            }
        }

        parent::removeChild($node);
    }
}
