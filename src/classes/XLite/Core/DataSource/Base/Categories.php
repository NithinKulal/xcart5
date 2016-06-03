<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\DataSource\Base;

/**
 * Abstract categories collection
 * Implements SeekableIterator and Countable interfaces
 */
abstract class Categories extends Collection
{
    /**
     * Performs a basic validation over a collection of categories
     * 
     * @return boolean
     */
    public function isValid()
    {
        $uniqueIds = array();
        $valid = true;
        // Check if each category has a unique id
        for (; $this->valid(); $this->next()) {
            $category = $this->current();

            if (0 >= $category['id'] || in_array($category['id'], $uniqueIds)) {
                $valid = false;
                break;
            }

            $uniqueIds[] = $category['id'];
        }

        $this->rewind();

        return $valid;
    }
}
