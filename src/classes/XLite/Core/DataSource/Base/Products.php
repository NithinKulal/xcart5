<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\DataSource\Base;

/**
 * Abstract products collection
 * Implements SeekableIterator and Countable interfaces
 */
abstract class Products extends Collection
{
    /**
     * Performs a basic validation over a collection of products
     * 
     * @return boolean
     */
    public function isValid()
    {
        $uniqueIds = array();
        $valid = true;
        // Check if each product has a unique id
        for (; $this->valid(); $this->next()) {
            $product = $this->current();

            if (0 >= $product['id'] || in_array($product['id'], $uniqueIds)) {
                $valid = false;
                break;
            }

            $uniqueIds[] = $product['id'];
        }

        $this->rewind();

        return $valid;
    }
}
