<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Logic\IntegrityCheck\Processor\Entry;

/**
 * Interface IEntry
 */
interface IEntry
{
    /**
     * @return array
     */
    public function getHashes();

    /**
     * @return \Iterator
     */
    public function getRealFiles();

    /**
     * @return array
     */
    public function getErrors();
}