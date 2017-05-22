<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic;

/**
 * Abstract repo step
 */
abstract class ARepoStep extends \XLite\Base implements \SeekableIterator, \Countable
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
     * @var \XLite\Logic\AGenerator
     */
    protected $generator;

    /**
     * Constructor
     *
     * @param \XLite\Logic\AGenerator $generator Generator OPTIONAL
     */
    public function __construct(\XLite\Logic\AGenerator $generator = null)
    {
        $this->generator = $generator;
    }

    /**
     * Stop
     */
    public function stop()
    {
    }

    /**
     * Finalize
     */
    public function finalize()
    {
    }

    // {{{ SeekableIterator, Countable

    /**
     * \SeekableIterator::seek
     *
     * @param integer $position Position
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
     * @return static
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
     */
    public function next()
    {
        $this->position++;
        $this->getItems()->next();
    }

    /**
     * \SeekableIterator::rewind
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
    abstract public function count();

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

        $row = $this->getItems()->current();
        $this->processModel($row[0]);

        $this->generator->getOptions()->time += round(microtime(true) - $time, 3);

        return true;
    }

    /**
     * Process model
     *
     * @param \XLite\Model\AEntity $model Model
     */
    abstract protected function processModel(\XLite\Model\AEntity $model);

    // }}}

    // {{{ Data

    /**
     * Get repository
     *
     * @return \XLite\Model\Repo\ARepo
     */
    abstract protected function getRepository();

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