<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Module\CDev\Egoods\View;

/**
 * Browse server file system
 */
abstract class BrowseServer extends \XLite\View\BrowseServer implements \XLite\Base\IDecorator
{
    /**
     * Return files entries structure
     * type      - 'catalog' or 'file' value
     * extension - extension of file entry. CSS class will be added according this parameter
     * name      - name of entry (catalog/file) inside the current catalog.
     *
     * Catalog entries go first in the entries list
     *
     * @return array
     */
    protected function getFSEntries()
    {
        $list = parent::getFSEntries();

        foreach ($list as &$row) {
            if (preg_match('/\.[a-f0-9]{32}$/Ss', $row['name'])) {
                $row['name'] = substr($row['name'], 0, -33);
                $row['egoods'] = true;
            }
        }

        return $list;
    }

    /**
     * Get file entry class
     *
     * @param array $entry Entry
     *
     * @return string
     */
    protected function getItemClass(array $entry)
    {
        return parent::getItemClass($entry)
            . (isset($entry['egoods']) && $entry['egoods'] ? ' egood-entry' : '');
    }
}

