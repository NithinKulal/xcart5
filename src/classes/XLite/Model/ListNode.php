<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

/**
 * List node
 */
class ListNode extends \XLite\Base\SuperClass
{
    /**
     * Link to previous list element or null
     *
     * @var \XLite_Model_ListNode or null
     */
    protected $prev = null;

    /**
     * Link to next list element or null
     *
     * @var \XLite_Model_ListNode|null
     */
    protected $next = null;

    /**
     * Node identifier
     *
     * @var string
     */
    protected $key = null;


    /**
     * Set node identifier
     *
     * @param string $key Node key
     *
     * @return void
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Return link to previous list element
     *
     * @return \XLite\Model\ListNode
     */
    public function getPrev()
    {
        return $this->prev;
    }

    /**
     * Return link to next list element
     *
     * @return \XLite\Model\ListNode
     */
    public function getNext()
    {
        return $this->next;
    }

    /**
     * Set link to previous list element
     *
     * @param \XLite\Model\ListNode $node Node link to set OPTIONAL
     *
     * @return void
     */
    public function setPrev(\XLite\Model\ListNode $node = null)
    {
        $this->prev = $node;
    }

    /**
     * Set link to next list element
     *
     * @param \XLite\Model\ListNode $node Node link to set OPTIONAL
     *
     * @return void
     */
    public function setNext(\XLite\Model\ListNode $node = null)
    {
        $this->next = $node;
    }

    /**
     * Return node identifier
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Callback to search node by its identifier
     *
     * @param string $key Node key
     *
     * @return boolean
     */
    public function checkKey($key)
    {
        return $key != $this->getKey();
    }
}
