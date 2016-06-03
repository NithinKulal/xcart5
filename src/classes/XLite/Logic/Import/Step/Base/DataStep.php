<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\Import\Step\Base;

/**
 * Abstract data import step
 */
abstract class DataStep extends \XLite\Logic\Import\Step\AStep
{
    /**
     * Last import processor (cache)
     *
     * @var   \XLite\Logic\Import\Processor\AProcessor
     */
    protected $lastProcessor;

    /**
     * Constructor
     *
     * @param \XLite\Logic\Import\Importer $importer Importer
     * @param integer                      $index    Step index
     *
     * @return void
     */
    public function __construct(\XLite\Logic\Import\Importer $importer, $index)
    {
        parent::__construct($importer, $index);

        $this->importer->getOptions()->rowsCount = $this->count();
    }

    /**
     * Get current processor
     *
     * @return \XLite\Logic\Import\Processor\AProcessor
     */
    protected function getProcessor()
    {
        if ($this->getOptions()->position != $this->lastPosition || !isset($this->lastProcessor)) {
            $i = $this->getOptions()->position;
            foreach ($this->importer->getProcessors() as $processor) {
                $this->lastProcessor = $processor;
                $count = $processor->count();

                if (0 >= $count) {
                    continue;
                }

                if ($i < $count) {
                    $processor->seek(max($i, 0));

                    if (!$processor->isEof()) {
                        break;
                    }
                }

                $i -= $count;
            }

            $this->lastPosition = $this->getOptions()->position;
        }

        return $this->lastProcessor;
    }

    /**
     * Check valid state of step
     *
     * @return boolean
     */
    public function isValid()
    {
        return parent::isValid()
            && $this->getProcessor()
            && $this->getProcessor()->isValid();
    }

    // {{{ Countable

    /**
     * \Counable::count
     *
     * @return integer
     */
    public function count()
    {
        $result = 0;
        foreach ($this->importer->getProcessors() as $processor) {
            $result += $processor->count();
        }

        return $result;
    }

    // }}}
}
