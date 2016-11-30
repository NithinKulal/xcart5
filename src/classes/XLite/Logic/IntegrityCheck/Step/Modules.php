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
 * Class Modules
 */
class Modules extends AStep
{

    /**
     * Process item
     *
     * @param \XLite\Model\Module $item
     *
     * @return void
     */
    protected function processItem($item)
    {
        $moduleEntry = new Entry\Module(
            $item->getActualName(),
            $item->getVersion()
        );

        $modified = IntegrityCheckProcessor::retrieveModifiedFiles($moduleEntry);

        $data = \XLite\Core\TmpVars::getInstance()->integrityCheckModulesData;
        $data = ($data && is_array($data))
            ? $data
            : [];

        $data[$item->getActualName()] = array_filter($modified);

        \XLite\Core\TmpVars::getInstance()->integrityCheckModulesData = $data;
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
            $cnd = new \XLite\Core\CommonCell();
            $cnd->{\XLite\Model\Repo\Module::P_INSTALLED} = true;

            $modules = \XLite\Core\Database::getRepo('XLite\Model\Module')->search($cnd);

            $this->items = new \ArrayIterator($modules);
            $this->items->rewind();
        }

        return $this->items;
    }
}