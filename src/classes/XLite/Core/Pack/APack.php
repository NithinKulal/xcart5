<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Pack;

/**
 * APack
 */
abstract class APack
{
    /**
     * Field names in metadata
     */
    const METADATA_FIELD_REVISION_DATE = 'RevisionDate';

    /**
     * Return pack name
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Return iterator to walk through directories
     *
     * @return \Iterator
     */
    abstract public function getDirectoryIterator();

    /**
     * Return pack metadata
     *
     * @return array
     */
    public function getMetadata()
    {
        return array(
            self::METADATA_FIELD_REVISION_DATE => \XLite\Core\Converter::time(),
        );
    }
}
