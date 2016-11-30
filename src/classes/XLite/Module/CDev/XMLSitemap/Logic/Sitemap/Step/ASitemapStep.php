<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\XMLSitemap\Logic\Sitemap\Step;

/**
 * Abstract step
 */
abstract class ASitemapStep extends AStep
{
    /**
     * Default priority
     */
    const DEFAULT_PRIORITY = 0.5;

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
                $options['count' . get_class($this)] = $this->getRepository()->countForSitemapGeneration();
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

        $this->processItem(array_pop($row));
        $this->generator->setInProgress(false);

        $this->generator->getOptions()->time += round(microtime(true) - $time, 3);

        return true;
    }

    /**
     * Process item
     *
     * @param mixed $item
     */
    abstract protected function processItem($item);

    /**
     * Process priority
     *
     * @param mixed $priority Link priority
     *
     * @return string
     */
    public static function processPriority($priority)
    {
        $priority = is_numeric($priority) ? round(doubleval($priority), 1) : static::DEFAULT_PRIORITY;
        if (1 < $priority || 0 > $priority) {
            $priority = static::DEFAULT_PRIORITY;
        }

        return strval($priority);
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
            $this->items = $this->getRepository()->getSitemapGenerationIterator($this->position);
            $this->items->rewind();
        }

        return $this->items;
    }

    // }}}
}