<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\QuickData\Step;

/**
 * Abstract step
 */
abstract class AStep extends \XLite\Logic\ARepoStep
{
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
    
    // {{{ Row processing
    
    protected function processModel(\XLite\Model\AEntity $model)
    {
        $model->updateQuickData();
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
            $this->items = $this->getRepository()->getQuickDataIterator($this->position);
            $this->items->rewind();
        }

        return $this->items;
    }

    // }}}
}
