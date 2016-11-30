<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\IntegrityCheck\Step;


abstract class AStep extends \XLite\Base implements \SeekableIterator, \Countable
{
    /**
     * Position
     *
     * @var integer
     */
    protected $position = 0;

    /**
     * Items iterator
     *
     * @var \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    protected $items;

    /**
     * Count (cached)
     *
     * @var integer
     */
    protected $countCache;

    /**
     * Generator
     *
     * @var \XLite\Logic\IntegrityCheck\Generator
     */
    protected $generator;

    /**
     * Constructor
     *
     * @param \XLite\Logic\IntegrityCheck\Generator $generator Generator OPTIONAL
     */
    public function __construct(\XLite\Logic\IntegrityCheck\Generator $generator = null)
    {
        $this->generator = $generator;
    }

    /**
     * Finalize
     *
     * @return void
     */
    public function finalize()
    {
    }

    // {{{ SeekableIterator, Countable

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
     *
     * @return void
     */
    public function seek($position)
    {
        if ($this->position != $position) {
            if ($position < $this->count()) {
                $this->position = $position;
                $this->getItems(true);
            }
        }
    }

    /**
     * \SeekableIterator::current
     *
     * @return \XLite\Logic\RemoveData\Step\AStep
     */
    public function current()
    {
        return $this;
    }

    /**
     * \SeekableIterator::key
     *
     * @return integer
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * \SeekableIterator::next
     *
     * @return void
     */
    public function next()
    {
        $this->position++;
        $this->getItems()->next();
    }

    /**
     * \SeekableIterator::rewind
     *
     * @return void
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * \SeekableIterator::valid
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->getItems()->valid();
    }

    /**
     * \Countable::count
     *
     * @return integer
     */
    public function count()
    {
        if (!isset($this->countCache)) {
            $options = $this->generator->getOptions();
            if (!isset($options['count' . get_class($this)])) {
                $options['count' . get_class($this)] = count($this->getItems());
                $this->generator->setOptions($options);
            }
            $this->countCache = $options['count' . get_class($this)];
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Row processing

    /**
     * Run step
     *
     * @return boolean
     */
    public function run()
    {
        $time = microtime(true);

        $this->generator->setInProgress(true);

        $row = $this->getItems()->current();

        $this->processItem($row);
        $this->generator->setInProgress(false);

        $this->generator->getOptions()->time += round(microtime(true) - $time, 3);

        return true;
    }

    /**
     * Process item
     *
     * @param mixed $item
     *
     * @return void
     */
    abstract protected function processItem($item);

    // }}}

    // {{{ Data

    /**
     * Get items iterator
     *
     * @param boolean $reset Reset iterator OPTIONAL
     *
     * @return \Doctrine\ORM\Internal\Hydration\IterableResult
     */
    abstract protected function getItems($reset = false);

    // }}}
}