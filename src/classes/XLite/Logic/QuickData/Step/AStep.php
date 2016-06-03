<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\QuickData\Step;

/**
 * Abstract export step
 */
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
     * @var \XLite\Logic\QuickData\Generator
     */
    protected $generator;

    /**
     * Constructor
     *
     * @param \XLite\Logic\QuickData\Generator $generator Generator OPTIONAL
     */
    public function __construct(\XLite\Logic\QuickData\Generator $generator = null)
    {
        $this->generator = $generator;
    }

    /**
     * Stop exporter
     *
     * @return void
     */
    public function stop()
    {
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
     * @return \XLite\Logic\QuickData\Step\AStep
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
            $this->countCache = $this->getRepository()->countForQuickData();
        }

        return $this->countCache;
    }

    // }}}

    // {{{ Row processing

    /**
     * Run export step
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
     *
     * @return void
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        $model->updateQuickData();
    }

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
    protected function getItems($reset = false)
    {
        if (!isset($this->items) || $reset) {
            $this->items = $this->getRepository()->getQuickDataIterator($this->position);
            $this->items->rewind();
        }

        return $this->items;
    }

    // }}}
}
