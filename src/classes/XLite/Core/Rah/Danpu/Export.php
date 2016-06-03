<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Rah\Danpu;

/**
 * Archive
 */
class Export extends \Rah\Danpu\Export
{
    /**
     * Opens a file for writing.
     *
     * @param  string $filename The filename
     * @param  string $flags    Flags
     *
     * @return void
     * @throws Exception
     */
    protected function open($filename, $flags)
    {
        parent::open($filename, $flags);

        $this->write('-- <?php die(); ?>');
    }
}
