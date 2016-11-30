<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\RemoveData\Step;


abstract class AStep extends \XLite\Logic\ARepoStep
{

    // {{{ SeekableIterator, Countable

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
                $options['count' . get_class($this)] = $this->getRepository()->countForRemoveData();
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

        $this->processModel($row[0]);
        $this->generator->setInProgress(false);

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
        $repo = $this->getRepository();
        $repo->delete($model, false);
    }

    // }}}

    // {{{ Data

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
            $this->items = $this->getRepository()->getRemoveDataIterator($this->position);
            $this->items->rewind();
        }

        return $this->items;
    }

    // }}}
}