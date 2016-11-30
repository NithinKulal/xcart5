<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\IntegrityCheck\Step;

use XLite\Logic\IntegrityCheck\Processor\Entry;
use XLite\Logic\IntegrityCheck\Processor\IntegrityCheckProcessor;

/**
 * Class Core
 */
class Core extends AStep
{

    /**
     * Process item
     *
     * @param \XLite\Logic\IntegrityCheck\Processor\Entry\Core $item
     *
     * @return void
     */
    protected function processItem($item)
    {
        \XLite\Core\TmpVars::getInstance()->integrityCheckCoreData = array_filter(
            IntegrityCheckProcessor::retrieveModifiedFiles($item)
        );
    }

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
            $this->items = new \ArrayIterator([
                new Entry\Core()
            ]);
            $this->items->rewind();
        }

        return $this->items;
    }
}